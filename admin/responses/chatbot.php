<?php

namespace MetagaussOpenAI\Admin\Responses;

class ChatBot extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function selectAssistantModal()
    {
        $this->checkNonce('select_assistant_modal');

        $after = '';

        if (!empty($_POST['after'])) {
            $after = '&after=' . sanitize_text_field($_POST['after']);
        }

        $url = 'https://api.openai.com/v1/assistants?limit=10' . $after;

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        $this->response['html'] = $this->getAssistantListHtml($output->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function getAssistantListHtml($list)
    {
        $assisstant_list = new \MetagaussOpenAI\Admin\Html\Elements\Chatbot\AssistantList();

        $html = '';

        foreach ($list as $assistant) {
            $assisstant_list->listItem($assistant);
            $html .= $assisstant_list->getHtml();
        }

        return $html;
    }

    public function saveChatbot()
    {
        $this->checkNonce('save_chatbot');
        $this->checkCapabilities();

        $chatbot_data = $this->cleanChatbotData();

        $insert = $this->sql->saveChatbot($chatbot_data);

        if ($insert === false) {
            global $wpdb;
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
        } else {
            $this->response['success'] = true;
            $this->response['chatbot_id'] = $insert;
        }
        
        echo wp_json_encode($this->response);
        wp_die();
    }

    private function cleanChatbotData()
    {
        $secure = new \MetagaussOpenAI\Admin\Secure\Chatbot();
        
        $id= $secure->chatbotId();
        $name = $secure->chatbotName();
        $description = $secure->chatbotDescription();
        $assistant_id = $secure->chatbotAssistantId();
        
        $errors = $secure->dataErrors();

        if (!empty($errors)) {
            $this->response['success'] = false;
            $this->response['message'] = __('There were errors in your data.', 'metagauss-openai');
            $this->response['errors'] = $errors;
            echo wp_json_encode($this->response);
            wp_die();
        }
        
        return array(
            'chatbot_id' => $id,
            'chatbot_name' => $name,
            'chatbot_description' => $description,
            'assistant_id' => $assistant_id
        );
    }
   
    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_selectAssistantModal', array($this, 'selectAssistantModal'));
        add_action('wp_ajax_saveChatbot', array($this, 'saveChatbot'));
        // add_action('wp_ajax_updateChatbot', array($this, 'updateChatbot'));
    }
}