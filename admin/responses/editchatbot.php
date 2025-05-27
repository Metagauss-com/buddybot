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
            $errorMessage = '<span class="buddybot-fw-bold">' . esc_html__('There was an error with your submission. Please fix the following errors:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
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

        $buddybot_prompt = new \BuddyBot\Admin\Prompt\OpenAiPrompt();
        $instructions = $buddybot_prompt->getHtml($buddybot_data);

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
                    'type' => 'file_search',
                    'file_search' => array(
                        'ranking_options' => array(
                            'score_threshold' => 0.2
                        ),
                    ),
                )
            ),
            'temperature' => floatval($buddybot_data["assistant_temperature"]),
            'top_p' => floatval($buddybot_data["assistant_topp"]),
            'metadata' => array(
                'aditional_instructions' => (string) ($buddybot_data["additional_instructions"] ?? ''),
                'openaisearch_msg' => (string) ($buddybot_data["openaisearch_msg"] ?? ''),
                'response_length' => (string) ($buddybot_data["response_length"] ?? ''),
                'language_option' => (string) ($buddybot_data["language_option"] ?? ''),

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
            'multilingual_support' => isset($chatbot_data['multilingual_support']) ? $chatbot_data['multilingual_support'] : '',
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
    
        $html = '<div class="buddybot-assistant-preview   ">';
    
        // Basic Info
        $html .= '<div class="buddybot-assistant-preview-header")' .'>';
        $html .= '<div class="buddybot-fw-bold buddybot-fs-2 buddybot-pb-5">' . esc_html__('Assistant Summary', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        $html .= '<p id="buddybot-existing-assistant-id" data-assistant-id="' . esc_attr($id) . '"><span class="buddybot-fw-bold">' . esc_html__('ID:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $id . '</p>';
        $html .= '<p><span class="buddybot-fw-bold">' . esc_html__('Name:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $name . '</p>';
        $html .= '<p><span class="buddybot-fw-bold">' . esc_html__('Description:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $description . '</p>';
        $html .= '<p id="buddybot-existing-assistant-model" data-model="' . esc_attr($model) . '"><span class="buddybot-fw-bold">' . esc_html__('Model:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $model . '</p>';
        $html .= '<p><span class="buddybot-fw-bold">' . esc_html__('Temperature:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $temperature . '</p>';
        $html .= '<p><span class="buddybot-fw-bold">' . esc_html__('Top P:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $top_p . '</p>';
        $html .= '<p><span class="buddybot-fw-bold">' . esc_html__('Instructions:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $instructions . '</p>';
        
    
        if (!empty($metadata) || !empty($tools)) {
            $html .= '<button type="button" class="toggle-more-details">';
            $html .= esc_html__('More Details', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            $html .= '<svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56-184-184Z"/></svg>';
            $html .= '</button>';

        }
        $html .= '</div>'; // Close header
       
    
        // Hidden Details
        $html .= '<div class="buddybot-more-details buddybot-mt-2" style="display:none;">';
    
        if (!empty($metadata)) {
            $html .= '<div class="buddybot-fs-2 buddybot-fw-bold">' . esc_html__('Metadata', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            foreach ($metadata as $key => $value) {
                $html .= '<p><span class="buddybot-fw-bold">' . esc_html($key) . ':</span> ' . esc_html($value) . '</p>';
            }
        }
    
        if (!empty($tools)) {
            $html .= '<div class="buddybot-fs-2 buddybot-fw-bold ">' . esc_html__('Tools', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            foreach ($tools as $tool) {
                $tool_type = isset($tool->type) ? esc_html($tool->type) : 'Unknown';
                $html .= '<p><span class="buddybot-fw-bold">' . esc_html__('Type:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span> ' . $tool_type . '</p>';
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
