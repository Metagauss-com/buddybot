<?php

namespace BuddyBot\Admin\Responses;

class EditChatBot extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getModels()
    {
        $this->checkNonce('get_models');
    
        if(empty($this->api_key)){
            $this->response['success'] = false;
            // Translators: %s represents the settings page link.
            $this->response['message'] = sprintf(
                esc_html__('To create or edit an assistant, you need to configure your OpenAI API key in the %s.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                '<a href="admin.php?page=buddybot-settings">' . esc_html__('settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>'
            );
            $this->response['empty_key'] = true;
            $this->response['html'] = '<option value="" disabled selected>' . esc_html__('No API key configured', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/models';

        $headers = [
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if ($this->openai_response_body->object === 'list') {
            $this->response['success'] = true;
            $this->response['list'] = $this->openai_response_body->data;
            $this->response['html'] = $this->modelsListHtml($this->openai_response_body->data);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to fetch models list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function saveBuddyBot()
    {
        $this->checkNonce('save_buddybot');
        $this->checkCapabilities();

        $buddybot_data = json_decode(wp_unslash(sanitize_text_field($_POST['buddybot_data'])), true);

        if (!is_array($buddybot_data)) {
            $this->response['success'] = false;
            $this->response['message'] = array(__('Invalid data: Data must be in array format.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
            echo wp_json_encode($this->response);
            wp_die();
        }

        $secure = new \BuddyBot\Admin\Secure\EditChatBot();
        $buddybot_data = $secure->secureData($buddybot_data);
        $errors = $secure->getErrors();

        if (!empty($errors)) {
            $this->response['success'] = false;
            $errorMessage = '<strong>' . esc_html__('There was an error with your submission. Please fix the following errors:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong>';
            $errorMessage .= '<ul style="list-style-type: disc; padding-left: 20px;">';
            foreach ($errors as $error) {
                $errorMessage .= '<li>' . esc_html($error) . '</li>'; 
            }
            $errorMessage .= '</ul>';

            $this->response['message'] = $errorMessage;
            echo wp_json_encode($this->response);
            wp_die();
        }

        if (!empty($buddybot_data['existing_assistant']) && $buddybot_data['existing_assistant'] == "1") {
            $chatbot_data = $this->extractDatabaseData($buddybot_data);
            $chatbot_data['assistant_id'] = $buddybot_data['connect_assistant'];
    
            if ($chatbot_data['id'] === false) {
                $this->createBuddyBot($chatbot_data);
            } else {
                $this->updateBuddyBot($chatbot_data);
            }
    
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Existing assistant linked successfully.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $assistant_id = '';

        if (!empty($buddybot_data['assistant_id'])) {
            $assistant_id = '/' . $buddybot_data['assistant_id'];
        }

        $url = 'https://api.openai.com/v1/assistants' . $assistant_id;

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $buddybot_prompt = new \BuddyBot\Admin\Responses\Prompt\OpenAiPrompt();
        $instructions = $buddybot_prompt->getHtml($buddybot_data);

        //$instructions .= "Personalized options: " . (isset($buddybot_data["personalized_options"]) && !empty($buddybot_data["personalized_options"]) ? "Enabled" : "Disabled") . ". ";
       // $instructions .= "Fallback behavior: " . esc_html($fallback_text) . ". ";
      // $instructions = "";

       // Emotion Detection
    //    $emotion_enabled = !empty($buddybot_data["emotion_detection"]);
    //    $instructions .= "2. Emotion Detection: " . ($emotion_enabled ? "Enabled" : "Disabled") . ". ";
    //    $instructions .= $emotion_enabled 
    //        ? "Adapt your tone subtly to reflect user sentiment. "
    //        : "Do not attempt to interpret or reflect user emotions. ";
       
    //    // Assistant Identity
    //    $assistant_name = !empty($buddybot_data["assistant_name"]) ? trim($buddybot_data["assistant_name"]) : "BuddyBot";
    //    $instructions .= 'Respond as ' . esc_html($assistant_name) . ' without referring to yourself as an assistant, bot, or AI. Do not mention your name unless explicitly asked. ';
       
    //    // Greeting Message
    //    $greeting = !empty($buddybot_data["greeting_message"]) ? trim($buddybot_data["greeting_message"]) : "";
    //    if ($greeting) {
    //        $instructions .= 'Only use the following greeting if the user does not begin with a direct question or command: "' . esc_html($greeting) . '". Otherwise, skip the greeting and respond directly to the user\'s query. ';
    //    }
       
       // OpenAI Search Handling
    //    $openai_disabled = !empty($buddybot_data["openai_search"]); // true = disallowed
    //    $fallback_msg = !empty($fallback_msg) ? trim(wp_unslash($fallback_msg)) : 'Sorry, I couldn\'t find any relevant information.';
       
    //    if ($openai_disabled) {
    //         $instructions .= "OpenAI Search: Disabled. You are strictly prohibited from using OpenAI or any external sources. ";
    //         $instructions .= "You must answer only using the internal vector store (synced content or uploaded files). ";
    //         $instructions .= "If no answer is found in the vector store, respond with this exact fallback message: \"" . esc_html($fallback_msg) . "\". ";
    //         $instructions .= "Do not generate a fallback message on your own. Use the one provided, verbatim. ";
    //         $instructions .= "Never mention whether the source was or wasn't found.";
    //     } else {
    //         $instructions .= "OpenAI Search: Enabled. First attempt to answer using internal data (vector store). ";
    //         $instructions .= "Only use OpenAI if no relevant internal content is found. Prioritize vector-based answers over OpenAI results.";
    //     }
       
    //     // Additional Instructions
    //     if (!empty($buddybot_data["additional_instructions"])) {
    //         $instructions .= " " . trim($buddybot_data["additional_instructions"]);
    //     }
       
       // $instructions .= "Multilingual support: " . (isset($buddybot_data["multilingual_support"]) && !empty($buddybot_data["multilingual_support"]) ? "Enabled" : "Disabled.Respond only in English,Do not respond in any other language even if the user inputs in a different language") . ". ";

       if (empty($buddybot_data["vectorstore_id"])) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Missing AI Training Knowledgebase ID or AI Training Knowledgebase Not Created.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $data = array(
            'model' => $buddybot_data["assistant_model"],
            'name' => $buddybot_data["assistant_name"],
            'description' => $buddybot_data["assistant_name"],
            'instructions' => $instructions,
            'tools' => array(
                array(
                    'type' => 'file_search'
                )
            ),
            'temperature' => floatval($buddybot_data["assistant_temperature"]),
            'top_p' => floatval($buddybot_data["assistant_topp"]),
            'metadata' => array(
                'aditional_instructions' => (string) ($buddybot_data["additional_instructions"] ?? ''),
                'openaisearch_msg' => (string) ($buddybot_data["openaisearch_msg"] ?? '')
            ),
            'tool_resources' => array(
                'file_search' => array(
                    'vector_store_ids' => array($buddybot_data["vectorstore_id"]),
                ),
            )
        );

        $args = [
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'method' => 'POST'
        ];

        $this->openai_response = wp_remote_post($url, $args);
        $this->processResponse();

        if (isset($this->openai_response_body) && isset($this->openai_response_body->id)) {

            $chatbot_data = $this->extractDatabaseData($buddybot_data);
            $chatbot_data['assistant_id'] = $this->openai_response_body->id;

            if ($chatbot_data['id'] === false) {
                $this->createBuddyBot($chatbot_data);
            } else {
                $this->updateBuddyBot($chatbot_data);
            }

        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function extractDatabaseData($chatbot_data)
    {
        return [
            'id' => isset($chatbot_data['buddybot_id']) ? $chatbot_data['buddybot_id'] : null,
            'chatbot_name' => isset($chatbot_data['buddybot_name']) ? $chatbot_data['buddybot_name'] : '',
            'chatbot_description' => isset($chatbot_data['buddybot_description']) ? $chatbot_data['buddybot_description'] : '',
            'assistant_model' => isset($chatbot_data['assistant_model']) ? $chatbot_data['assistant_model'] : '',
            'emotion_detection' => isset($chatbot_data['emotion_detection']) ? $chatbot_data['emotion_detection'] : '',
            'greeting_message' => isset($chatbot_data['greeting_message']) ? $chatbot_data['greeting_message'] : '',
            'openai_search' => isset($chatbot_data['openai_search']) ? $chatbot_data['openai_search'] : '',
            'external' => isset($chatbot_data['existing_assistant']) ? $chatbot_data['existing_assistant'] : '',
        ];
    }

    private function createBuddyBot($chatbot_data)
    {
        $insert = $this->sql->createBuddyBot($chatbot_data);

        if ($insert === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $insert;
        }
    }

    private function updateBuddyBot($chatbot_data)
    {
        $update = $this->sql->updateBuddyBot($chatbot_data);

        if ($update === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $chatbot_data['id'];
        }
    }

    private function modelsListHtml($list)
    {
        $unsupported_models = $this->config->getProp('unsupported_models');

        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $model) {

            if (!in_array($model->id, $unsupported_models)) {
                $html .= '<option value="' . esc_attr($model->id) . '">';
                $html .= esc_html(strtoupper(str_replace('-', ' ', $model->id)));
                $html .= '</option>';
            }
        }

        return $html;
    }

    public function getAssistantData()
    {
        $this->checkNonce('get_assistant_data');

        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';
        $buddybot_id = isset($_POST['buddybot_id']) && !empty($_POST['buddybot_id']) ? sanitize_text_field($_POST['buddybot_id']) : '';

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );

        $this->openai_response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));
        $this->processResponse();

        //print_r($this->openai_response_body);die;
        if (is_object($this->openai_response_body) && isset($this->openai_response_body->id)) {

            $buddybot_details = $this->getBuddyBot($buddybot_id);
            
            if($buddybot_details == false) {
                $this->response['success'] = false;
                echo wp_json_encode($this->response);
                wp_die();
            }
    
            $this->response['success'] = true;
            $this->response['result'] = $this->openai_response_body;
            if ($buddybot_details->external == 1) {
                $this->response['html'] = $this->selectedAssistantHtml($this->openai_response_body);
            }
            $this->response['local'] = $buddybot_details;
        }
        //print_r($this->response);die;

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function getBuddyBot($buddybot_id)
    {
        $buddybot = $this->sql->getBuddyBotById($buddybot_id);

        if ($buddybot === false) {
            global $wpdb;
            $this->response['message'] = !empty($wpdb->last_error) ? $wpdb->last_error : esc_html__('Incorrect Chatbot ID.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return false;
        }

        return $buddybot;
    }

    public function selectAssistantModal()
    {
        $this->checkNonce('select_assistant_modal');
        $this->checkOpenaiKey(__('Unable to fetch the list of Assistants. Please ensure your OpenAI API key is configured in the BuddyBot settings.','buddybot-ai-custom-ai-assistant-and-chat-agent'));

        $after = '';

        if (!empty($_POST['after'])) {
            $after = '&after=' . sanitize_text_field($_POST['after']);
        }

        $url = 'https://api.openai.com/v1/assistants?limit=10' . $after;
        
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];
        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if (!empty($this->openai_response_body->data)) {
            $this->response['success'] = true;
            $this->response['html'] = $this->getAssistantListHtml($this->openai_response_body->data);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('No Assistant Created, Please create new assistant to use this feature.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function getAssistantListHtml($list)
    {
        $assisstant_list = new \BuddyBot\Admin\Html\Elements\Chatbot\AssistantList();

        $html = '';

        foreach ($list as $assistant) {
            $assisstant_list->listItem($assistant);
            $html .= $assisstant_list->getHtml();
        }

        return $html;
    }

    public function selectedAssistant()
    {
        $this->checkNonce('selected_assistant');

        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );

        $this->openai_response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));

        $this->processResponse();

        $this->response['success'] = true;
        $this->response['html'] = $this->selectedAssistantHtml($this->openai_response_body);

        echo wp_json_encode($this->response);
        wp_die();

    }

    private function selectedAssistantHtml($output)
    {
        if (empty($output) || !is_object($output)) {
            return '<div class="buddybot-assistant-preview-error">' . esc_html__('No assistant data found.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        }
    
        // Basic fields
        $id           = !empty($output->id) ? esc_html($output->id) : 'N/A';
        $name         = !empty($output->name) ? esc_html($output->name) : 'N/A';
        $description  = !empty($output->description) ? esc_html($output->description) : 'N/A';
        $model        = !empty($output->model) ? esc_html($output->model) : 'N/A';
        $temperature  = isset($output->temperature) ? esc_html($output->temperature) : 'N/A';
        $top_p        = isset($output->top_p) ? esc_html($output->top_p) : 'N/A';
        $instructions = !empty($output->instructions) ? esc_html($output->instructions) : 'N/A';
    
        // Optional data
        $metadata = !empty($output->metadata) ? (array) $output->metadata : [];
        $tools    = !empty($output->tools) ? (array) $output->tools : [];
    
        $html = '<div class="buddybot-assistant-preview">';
    
        // Basic Info
        $html .= '<div class="">' . esc_html__('Assistant Summary', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        $html .= '<p id="buddybot-existing-assistant-id" data-assistant-id="' . esc_attr($id) . '"><strong>' . esc_html__('ID:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $id . '</p>';
        $html .= '<p><strong>' . esc_html__('Name:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $name . '</p>';
        $html .= '<p><strong>' . esc_html__('Description:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $description . '</p>';
        $html .= '<p id="buddybot-existing-assistant-model" data-model="' . esc_attr($model) . '"><strong>' . esc_html__('Model:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $model . '</p>';
        $html .= '<p><strong>' . esc_html__('Temperature:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $temperature . '</p>';
        $html .= '<p><strong>' . esc_html__('Top P:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $top_p . '</p>';
        $html .= '<p><strong>' . esc_html__('Instructions:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $instructions . '</p>';
    
        if (!empty($metadata) || !empty($tools)) {
            $html .= '<button type="button" class="toggle-more-details" data-show="' . esc_attr__('Show More Details', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '" data-hide="' . esc_attr__('Hide Details', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">' . esc_html__('Show More Details', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        }
    
        // Hidden Details
        $html .= '<div class="buddybot-more-details buddybot-mt-3" style="display:none;">';
    
        if (!empty($metadata)) {
            $html .= '<div class="">' . esc_html__('Metadata', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            foreach ($metadata as $key => $value) {
                $html .= '<p><strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '</p>';
            }
        }
    
        if (!empty($tools)) {
            $html .= '<div>' . esc_html__('Tools', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            foreach ($tools as $tool) {
                $tool_type = isset($tool->type) ? esc_html($tool->type) : 'Unknown';
                $html .= '<p><strong>' . esc_html__('Type:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . $tool_type . '</p>';
            }
        }
    
        $html .= '</div>'; // Close more details
    
        $html .= '</div>'; // Close preview wrapper
    
        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getModels', array($this, 'getModels'));
        add_action('wp_ajax_saveBuddyBot', array($this, 'saveBuddyBot'));
        add_action('wp_ajax_getAssistantData', array($this, 'getAssistantData'));
        add_action('wp_ajax_selectAssistantModal', array($this, 'selectAssistantModal'));
        add_action('wp_ajax_selectedAssistant', array($this, 'selectedAssistant'));
    }
}