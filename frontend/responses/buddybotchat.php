<?php
namespace BuddyBot\Frontend\Responses;

class BuddybotChat extends \BuddyBot\Frontend\Responses\Moroot
{
    public function getConversationList()
    {
        $buddybot_chat = \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat::getInstance();
        $timezone = sanitize_text_field(wp_unslash($_POST['timezone']));
        $buddybot_chat->conversationList($timezone);
        wp_die();
    }

    public function getMessages()
    {
        $this->checkNonce('get_messages');
    
        $thread_id = sanitize_text_field($_POST['thread_id']);
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
        $chat_bubble = new \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\Messages();
        $chat_bubble->setMessage($message);
        return $chat_bubble->getHtml();
    }

    public function sendUserMessage()
    {
        $this->checkNonce('send_user_message');

        if (empty($_POST['thread_id'])) {
            $this->createThreadWithMessage();
        } else {
            $this->addMessageToThread();
        }
    }

    private function createThreadWithMessage()
    {
        $url = 'https://api.openai.com/v1/threads';
    
        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        // Prepare the data
        $data = array(
            'metadata' => array(
                'wp_user_id' => (string)get_current_user_id(),
                'wp_source' => 'frontend'
            )
        );
    
        // Perform the POST request
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body'    => wp_json_encode($data),
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
    
        $save_thread = $this->sql->saveThreadInDb($this->response['result']->id);
    
        if ($save_thread === false) {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to save conversation in database.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
    
        $this->addMessageToThread($this->response['result']->id);
        wp_die();
    }
    

    private function addMessageToThread($thread_id = false)
    {
        if ($thread_id === false) {
            $thread_id = sanitize_text_field($_POST['thread_id']);
        }

        $user_message = sanitize_textarea_field(wp_unslash($_POST['user_message']));
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );

        // Prepare the data
        $data = array(
            'role' => 'user',
            'content' => $user_message,
            'metadata' => array(
                'wp_user_id' => (string)get_current_user_id(),
                'wp_source' => 'frontend',
            ),
        );

        // Perform the POST request
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body'    => wp_json_encode($data),
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
        
        $this->sql->updateThreadName($thread_id, $user_message);

        $this->response['html'] = $this->chatBubbleHtml($output);
        echo wp_json_encode($this->response);
        wp_die();
    }


    public function createFrontendRun()
    {
        $thread_id = sanitize_text_field($_POST['thread_id']);
        $assistant_id = sanitize_text_field($_POST['assistant_id']);
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';
    
        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        // Prepare the data
        $data = array(
            'assistant_id' => $assistant_id,
            'metadata' => array(
                'wp_user_id' => (string)get_current_user_id(),
                'wp_source' => 'frontend',
            ),
        );
    
        // Perform the POST request
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body'    => wp_json_encode($data),
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
    
        echo wp_json_encode($this->response);
        wp_die();
    }
    

    public function retrieveFrontendRun()
    {
        $this->checkNonce('retrieve_run');
    
        $thread_id = sanitize_text_field($_POST['thread_id']);
        $run_id = sanitize_text_field($_POST['run_id']);
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;
    
        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );

        $maxRetries = 5;
        $retryInterval = 2;
        $attempt = 0;
    
        while ($attempt < $maxRetries) {
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
    
        switch ($output->status) {
            case 'completed':
                $this->response['success'] = true;
                $this->response['status'] = 'completed';
                echo wp_json_encode($this->response);
                wp_die();
            break;
            
            case 'failed':
                $this->response['success'] = false;
                if (isset($output->last_error) && isset($output->last_error->message)) {
                    $this->response['message'] = $output->last_error->message;
                } else {
                    $this->response['message'] = esc_html__('Run failed: Unknown error.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                }
                echo wp_json_encode($this->response);
                wp_die();
            break;

            case 'queued':
            case 'in_progress':

                $attempt++;
                if ($attempt >= $maxRetries) {
                    // If max retries reached, return error
                    $this->response['success'] = false;
                    $this->response['message'] = sprintf(esc_html__('Run status still in progress after %d attempts.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), $maxRetries);
                    echo wp_json_encode($this->response);
                    wp_die();
                }

                sleep($retryInterval);
            break;

            default:
                $this->response['success'] = false;
                $this->response['message'] = sprintf(esc_html__('Unexpected status: %s', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($output->status));
                echo wp_json_encode($this->response);
                wp_die();
            break;
        }
    }
}
    

    public function deleteFrontendThread()
    {
        $this->checkNonce('delete_frontend_thread');
    
        $thread_id = sanitize_text_field($_POST['thread_id']);
        $user_id = get_current_user_id();
    
        if ($this->sql->isThreadOwner($thread_id, $user_id) === false) {
            $this->response['success'] = false;
            $this->response['message'] = __('You are not authorized to delete this thread.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
    
        $url = 'https://api.openai.com/v1/threads/' . $thread_id;
    
        // Prepare the headers
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2',
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
            $this->sql->deleteThread($thread_id);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to delete conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        echo wp_json_encode($this->response);
        wp_die();
    }


    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getConversationList', array($this, 'getConversationList'));
        add_action('wp_ajax_getMessages', array($this, 'getMessages'));
        add_action('wp_ajax_sendUserMessage', array($this, 'sendUserMessage'));
        add_action('wp_ajax_createFrontendRun', array($this, 'createFrontendRun'));
        add_action('wp_ajax_retrieveFrontendRun', array($this, 'retrieveFrontendRun'));
        add_action('wp_ajax_deleteFrontendThread', array($this, 'deleteFrontendThread'));

        add_action('wp_ajax_nopriv_getConversationList', array($this, 'getConversationList'));
        add_action('wp_ajax_nopriv_getMessages', array($this, 'getMessages'));
        add_action('wp_ajax_nopriv_sendUserMessage', array($this, 'sendUserMessage'));
        add_action('wp_ajax_nopriv_createFrontendRun', array($this, 'createFrontendRun'));
        add_action('wp_ajax_nopriv_retrieveFrontendRun', array($this, 'retrieveFrontendRun'));
        add_action('wp_ajax_nopriv_deleteFrontendThread', array($this, 'deleteFrontendThread'));
    }
}