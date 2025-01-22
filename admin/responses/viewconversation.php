<?php

namespace BuddyBot\Admin\Responses;

class ViewConversation extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getRelatedConversationMsg()
    {

        $this->checkNonce('get_related_conversation_msg');

        $thread_id = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';
        $user_id = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : '';

        $conversation = $this->sql->getTotalConversationsCountExcludingThread($thread_id, $user_id);

        if ($conversation === false) {
            global $wpdb;
            $response = array(
                'success' => false,
                'message' => $wpdb->last_error
            );
            echo wp_json_encode($response);
            wp_die();
        }

        if (!empty($conversation)) {
            $this->response['success'] = true;
            $this->response['message'] = sprintf(
                wp_kses_post(__('There are %d more conversations related to this user. To check please click <a href="%s">Here</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),
                intval($conversation), 
                esc_url(admin_url('admin.php?page=buddybot-conversations&filter=true&user_id=' . $user_id))
            );
        } else {
            $this->response['success'] = true;
            $this->response['disabled'] = true;
            $this->response['message'] = esc_html__('There are no more conversations related to this user.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }     

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function listConversation()
    {
        $this->checkNonce('list_conversation');
    
        $thread_id = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';
        $limit = absint($_POST['limit']);
        $order = sanitize_text_field($_POST['order']);
        $after = '';
        $before = '';
    
        if (!empty($_POST['after'])) {
            $after = '&after=' . sanitize_text_field($_POST['after']);
        }
    
        if (!empty($_POST['before'])) {
            $before = '&before=' . sanitize_text_field($_POST['before']);
        }
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages?limit=' . $limit . '&order=' . $order . $after . $before;
    
        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        // Perform the GET request
        $response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));
    
        // Check for errors
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }
    
        // Decode the response body
        $output = json_decode(wp_remote_retrieve_body($response));
    
        // Check for errors in the output
        $this->checkError($output);
    
        $this->messagesHtml($output->data);
    
        echo wp_json_encode($this->response);
        wp_die();
    }

    private function messagesHtml($messages)
    {
        $html = '';
        $messages = array_reverse($messages);
        foreach ($messages as $message) {
            $html .= $this->chatBubbleHtml($message);
        }

        $this->response['html'] = $html;
    }

    private function chatBubbleHtml($message)
    {
        $chat_bubble = new \BuddyBot\Admin\Html\Elements\Playground\ChatBubble();
        $chat_bubble->setMessage($message);
        return $chat_bubble->getHtml();
    }

    public function deleteConversation()
    {
        $this->checkNonce('delete_conversation');
        $thread_id = isset($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';
    
        $url = 'https://api.openai.com/v1/threads/' . $thread_id;
    
        // Prepare the headers
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );
    
        // Perform the DELETE request
        $response = wp_remote_request($url, array(
            'method'  => 'DELETE',
            'headers' => $headers,
            'timeout' => 60,
        ));
    
        // Check for errors
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }
    
        // Decode the response body
        $output = json_decode(wp_remote_retrieve_body($response));
    
        // Check for errors in the output
        $this->checkError($output);
    
        if (isset($output->deleted) && $output->deleted) {
            $this->response['success'] = true;
            $this->sql->deleteConversation($thread_id);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to delete conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_listConversation', array($this, 'listConversation'));
        add_action('wp_ajax_getRelatedConversationMsg', array($this, 'getRelatedConversationMsg'));
        add_action('wp_ajax_deleteConversation', array($this, 'deleteConversation'));
    }
}