<?php

namespace BuddyBot\Admin\Responses;

class BuddyBots extends \BuddyBot\Admin\Responses\MoRoot
{

    public function deleteBuddyBot()
    {
        $this->checkNonce('delete_buddybot');
        $this->checkCapabilities();

        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';
        $chatbot_id = isset($_POST['chatbot_id']) && !empty($_POST['chatbot_id']) ? intval($_POST['chatbot_id']) : 0;

        if (empty($assistant_id) || $chatbot_id <= 0) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Assistant ID/ChatBot ID cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers, 'method' => 'DELETE'];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }

        $output = json_decode(wp_remote_retrieve_body($response));
        $notFound = false;
        if (isset($output->error) && isset($output->error->message)) {
            if (strpos(strtolower($output->error->message), 'no assistant found') !== false) {
                $notFound = true;
            }
        }

        if (!$notFound) {
            $this->checkError($output);
        }
        
        if (isset($output->deleted) && $output->deleted || $notFound) {
            
            $response = $this->sql->deleteChatbot($chatbot_id);
            if ($response === false) {
                global $wpdb;
                $this->response['success'] = false;
                $this->response['message'] = $wpdb->last_error;
                echo wp_json_encode($this->response);
                wp_die();
            } 
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Successfully deleted BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to delete the BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();

    }

    public function savePaginationLimit()
    {
        $this->checkNonce('pagination_dropdown');

        $limit = isset($_POST['selected_value']) ? absint($_POST['selected_value']) : 10;

        if (!update_option('buddybot_columns_per_page', $limit)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Failed to update the setting.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
        //update_option('buddybot_columns_per_page', $limit);
        $this->response['success'] = true;

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_deleteBuddyBot', array($this, 'deleteBuddyBot'));
        add_action('wp_ajax_savePaginationLimit', array($this, 'savePaginationLimit'));
    }
}