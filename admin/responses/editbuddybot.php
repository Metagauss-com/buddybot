<?php

namespace BuddyBot\Admin\Responses;

class EditBuddyBot extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getModels()
    {
        $this->checkNonce('get_models');
    
        if(empty($this->api_key)){
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('To create or edit an assistant, you need to configure your OpenAI API key in the BuddyBot settings.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
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

        $buddybot_data = $this->cleanBuddyBotData();

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

        $fallback_behavior_map = [
            "ask" => "Ask for clarification",
            "generic" => "Provide a generic response",
            "escalate" => "Escalate to support"
        ];
        $fallback_key = $buddybot_data["fallback_behavior"] ?? "generic";
        $fallback_text = $fallback_behavior_map[$fallback_key] ?? "Provide a generic response";

        $instructions = '';

        $instructions .= "Personalized options: " . (isset($buddybot_data["personalized_options"]) && !empty($buddybot_data["personalized_options"]) ? "Enabled" : "Disabled") . ". ";
       // $instructions .= "Fallback behavior: " . esc_html($fallback_text) . ". ";
        $instructions .= "Emotion detection: " . (isset($buddybot_data["emotion_detection"]) && !empty($buddybot_data["emotion_detection"]) ? "Enabled" : "Disabled") . ". ";
        $instructions .= "Greeting message: " . (isset($buddybot_data["greeting_message"]) && !empty($buddybot_data["greeting_message"]) ? $buddybot_data["greeting_message"] : "") . ". ";
        $instructions .= "Allow assistant to seek answers from OpenAI: " . (isset($buddybot_data["openai_search"]) && !empty($buddybot_data["openai_search"]) ? "Enabled" : "Disabled") . ". ";
        if (!empty($buddybot_data["openai_search"])) {
            $instructions .= "";
        } else {
            $instructions .= "You must only provide answers from the uploaded files (vector store). Do not query OpenAI or any other sources. If an answer is not found in the uploaded files, provide a fallback response.";
        }
        //$instructions .= $buddybot_data["additional_instruction"];
       // $instructions .= "Multilingual support: " . (isset($buddybot_data["multilingual_support"]) && !empty($buddybot_data["multilingual_support"]) ? "Enabled" : "Disabled.Respond only in English,Do not respond in any other language even if the user inputs in a different language") . ". ";

        $data = array(
            'model' => $buddybot_data["assistant_model"],
            'name' => $buddybot_data["assistant_name"],
            'description' => $buddybot_data["assistant_name"],
            'instructions' => $instructions,
            "tools" => array(
                array(
                    "type" => "file_search"
                )
            ),
            'temperature' => $buddybot_data["assistant_temperature"],
            'top_p' => $buddybot_data["assistant_topp"],
            'metadata' => array(
                'aditional_instructions' => $buddybot_data["additional_instruction"]
            ),
        );

        if (empty($buddybot_data["vectorstore_id"])) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Missing AI Training Knowledgebase ID or AI Training Knowledgebase Not Created.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $data['tool_resources'] = array(
            'file_search' => array(
                'vector_store_ids' => array($buddybot_data["vectorstore_id"]),
            ),
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
            //print_r($chatbot_data['id']);die;

            if ($chatbot_data['id'] === false) {
                $this->createBuddyBot($chatbot_data);
            } else {
                $this->updateBuddyBot($chatbot_data);
            }

        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function cleanBuddyBotData()
    {
        $secure = new \BuddyBot\Admin\Secure\EditBuddyBot();
        
        $id= $secure->buddybotId();
        $buddybot_name = $secure->buddybotName();
        $buddybot_description = $secure->buddybotDescription();
        $assistant_name = $secure->assistantName();
        $assistant_model = $secure->assistant_model();
        $additional_instruction = $secure->additionalInstructions();
        $assistant_temperature = $secure->assistantTemperature();
        $assistant_topp = $secure->topP();
        $openai_search = $secure->openAiSearch();
        $personalized_options = $secure->personalizedOptions();
        $fallback_behavior = $secure->fallbackBehavior();
        $emotion_detection = $secure->emotionDetection();
        $greeting_message = $secure->greetingMessage();
        //$multilingual_support = $secure->multilingualSupport();
        $vectorstore_id = $secure->vectorstoreId();
        $assistant_id = $secure->assistantId();
        
        $errors = $secure->dataErrors();

        //print_r($errors);die;
        if (!empty($errors)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('There were errors in your data.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            $this->response['message'] .= $errors;
            echo wp_json_encode($this->response);
            wp_die();
        }
        
        return array(
            'id' => $id,
            'chatbot_name' => $buddybot_name,
            'chatbot_description' => $buddybot_description,
            'assistant_name' => $assistant_name,
            'assistant_model' => $assistant_model,
            'additional_instruction' => $additional_instruction,
            'assistant_temperature' => $assistant_temperature,
            'assistant_topp' => $assistant_topp,
            'openai_search' => $openai_search,
            'personalized_options' => $personalized_options,
            'fallback_behavior' => $fallback_behavior,
            'emotion_detection' => $emotion_detection,
            'greeting_message' => $greeting_message,
            //'multilingual_support' => $multilingual_support,
            'vectorstore_id' => $vectorstore_id,
            'assistant_id' => $assistant_id
        );
    }

    private function extractDatabaseData($chatbot_data)
    {
        return [
            'id' => $chatbot_data['id'],
            'chatbot_name' => $chatbot_data['chatbot_name'],
            'chatbot_description' => $chatbot_data['chatbot_description'],
            'assistant_model' => $chatbot_data['assistant_model'],
            'fallback_behavior' => $chatbot_data['fallback_behavior'],
            'personalized_options' => $chatbot_data['personalized_options'],
            'emotion_detection' => $chatbot_data['emotion_detection'],
            'greeting_message' => $chatbot_data['greeting_message'],
            'openai_search' => $chatbot_data['openai_search'],
           // 'multilingual_support' => $chatbot_data['multilingual_support'],      
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

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getModels', array($this, 'getModels'));
        add_action('wp_ajax_saveBuddyBot', array($this, 'saveBuddyBot'));
        add_action('wp_ajax_getAssistantData', array($this, 'getAssistantData'));
    }
}