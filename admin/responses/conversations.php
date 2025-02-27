<?php

namespace BuddyBot\Admin\Responses;

class Conversations extends \BuddyBot\Admin\Responses\MoRoot
{

    public function deleteConversation()
    {
        $this->checkNonce('delete_conversation');

        $thread_id = isset($_POST['thread_id']) && !empty($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';

        $url = 'https://api.openai.com/v1/threads/' . $thread_id;
    
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );
    
        $response = wp_remote_request($url, array(
            'method'  => 'DELETE',
            'headers' => $headers,
            'timeout' => 60,
        ));
    
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }
    
        $output = json_decode(wp_remote_retrieve_body($response));
        $notFound = false;
        if (isset($output->error) && isset($output->error->message)) {
            if (strpos(strtolower($output->error->message), 'no thread found') !== false) {
                $notFound = true;
            }
        }
    
        if (!$notFound) {
            $this->checkError($output);
        }
    
        if (isset($output->deleted) && $output->deleted || $notFound) {

            $response = $this->sql->deleteConversation($thread_id);
            if ($response === false) {
                global $wpdb;
                $this->response['success'] = false;
                $this->response['message'] = $wpdb->last_error;
                echo wp_json_encode($this->response);
                wp_die();
            } 
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Conversation deleted Successfully.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to delete conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        echo wp_json_encode($this->response);
        wp_die();
        
    }

    public function saveConversationLimit()
    {
        $this->checkNonce('pagination_dropdown');

        $limit = isset($_POST['selected_value']) ? absint($_POST['selected_value']) : 10;
        if(!empty($limit)){
            update_option('buddybot_conversations_per_page', $limit);
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Successfully saved conversation limit per page.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }else{
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Failed to save conversation limit per page.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_deleteConversation', array($this, 'deleteConversation'));
        add_action('wp_ajax_saveConversationLimit', array($this, 'saveConversationLimit'));
        
    }
}