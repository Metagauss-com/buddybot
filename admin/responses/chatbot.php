<?php

namespace BuddyBot\Admin\Responses;

class ChatBot extends \BuddyBot\Admin\Responses\MoRoot
{
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
            $this->response['message'] = $this->createAssistantHtml();
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function getAssistantListHtml($list)
    {

        // if ( empty($list) ) {
        //     echo "hello";
        // }
        $assisstant_list = new \BuddyBot\Admin\Html\Elements\Chatbot\AssistantList();

        $html = '';

        foreach ($list as $assistant) {
            $assisstant_list->listItem($assistant);
            $html .= $assisstant_list->getHtml();
        }

        return $html;
    }

    private function createAssistantHtml(){
        $html = '';

        $html .= '<div class="text-center small">' . esc_html__('No Assistant Created, Please create new assistant to use this feature.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') .'</div>';
        $html .=  '<div class="text-center">';
        $html .= '<a href="admin.php?page=buddybot-editassistant" class="btn btn-outline-dark btn-sm w-50 mx-auto my-3">' . esc_html__('Create Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>';
        $html .=  '</div>';

        return $html;
     
    }

    public function saveChatbot()
    {
        $this->checkNonce('save_chatbot');
        $this->checkCapabilities();

        $chatbot_data = $this->cleanChatbotData();
        
        if ($chatbot_data['id'] === false) {
            $this->createChatbot($chatbot_data);
        } else {
            $this->updateChatbot($chatbot_data);
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function cleanChatbotData()
    {
        $secure = new \BuddyBot\Admin\Secure\Chatbot();
        
        $id= $secure->chatbotId();
        $name = $secure->chatbotName();
        $description = $secure->chatbotDescription();
        $assistant_id = $secure->chatbotAssistantId();
        
        $errors = $secure->dataErrors();

        if (!empty($errors)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('There were errors in your data.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            $this->response['errors'] = $errors;
            echo wp_json_encode($this->response);
            wp_die();
        }
        
        return array(
            'id' => $id,
            'chatbot_name' => $name,
            'chatbot_description' => $description,
            'assistant_id' => $assistant_id
        );
    }

    private function createChatbot($chatbot_data)
    {
        $insert = $this->sql->createChatbot($chatbot_data);

        if ($insert === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $insert;
        }
    }

    private function updateChatbot($chatbot_data)
    {
        $update = $this->sql->updateChatbot($chatbot_data);

        if ($update === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $chatbot_data['id'];
        }
    }

    private function removeAssistantId($chatbot_id)
    {
        $update = $this->sql->removeAssistantId($chatbot_id);

        if ($update === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('The assistant linked to this BuddyBot no longer exists on the OpenAI server. Users attempting to interact with this BuddyBot will encounter an error. Please associate a new assistant to ensure proper functionality.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    }

    public function checkAssistant()
    {
        $this->checkNonce('check_assistant');

        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';
        $chatbot_id = isset($_POST['chatbot_id']) ? absint($_POST['chatbot_id']) : '';

        if (empty($chatbot_id)) {
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('No assistant has been created. Please create a new assistant first.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        if(empty($assistant_id) && !empty($chatbot_id) ){
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('The assistant linked to this BuddyBot no longer exists on the OpenAI server. Users attempting to interact with this BuddyBot will encounter an error. Please associate a new assistant to ensure proper functionality.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;
        
        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];
        $this->openai_response = wp_remote_get($url, $args);
        $json_response = $this->openai_response['body'];
        $decoded_response = json_decode($json_response, true);
        if (empty($decoded_response['id'])) {
             $this->removeAssistantId($chatbot_id);
        } else {
            $this->response['success'] = true;
        }

        echo wp_json_encode($this->response);
        wp_die();
    }
   
    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_selectAssistantModal', array($this, 'selectAssistantModal'));
        add_action('wp_ajax_saveChatbot', array($this, 'saveChatbot'));
        add_action('wp_ajax_checkAssistant', array($this, 'checkAssistant'));
    }
}