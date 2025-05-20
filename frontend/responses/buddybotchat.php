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
        $timezone = (isset($_POST['timezone']) && !empty($_POST['timezone'])) ? sanitize_text_field(wp_unslash($_POST['timezone'])) : '';
    
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
    
        $this->messagesHtml($output->data, $timezone);
    
        echo wp_json_encode($this->response);
        wp_die();
    }
    

    private function messagesHtml($messages, $timezone)
    {
        $html = '';
        $messages = array_reverse($messages);
        foreach ($messages as $message) {
            $html .= $this->chatBubbleHtml($message, $timezone);
        }

        $this->response['html'] = $html;
    }

    private function chatBubbleHtml($message, $timezone)
    {
        $chat_bubble = new \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\Messages();
        $chat_bubble->setMessage($message, $timezone);
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
        $timezone = (isset($_POST['timezone']) && !empty($_POST['timezone'])) ? sanitize_text_field(wp_unslash($_POST['timezone'])) : '';
        
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

        $this->response['html'] = $this->chatBubbleHtml($output, $timezone);
        echo wp_json_encode($this->response);
        wp_die();
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

    public function setCookieSession()
    {
        $this->checkNonce('set_cookie_session');

        $session_lifetime = $this->options->getOption('session_expiry', 24) * 3600;
        $cookie_data = [];
        $should_update_cookie = false;

        if (isset($_COOKIE['buddybot_session_data'])) {
            $cookie_data = json_decode(stripslashes($_COOKIE['buddybot_session_data']), true);

            if (!isset($cookie_data['session_id'])) {
                $cookie_data['session_id'] = bin2hex(random_bytes(16));
                $should_update_cookie = true;
            }

            if (isset($_POST['visitor_id'])) {
                $visitor_id = sanitize_text_field($_POST['visitor_id']);

                if (empty($visitor_id) || !$this->is_valid_email_format($visitor_id)) {
                    $this->response['success'] = false;
                    $this->response['message'] = __('Please enter a valid email address.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo wp_json_encode($this->response);
                    wp_die();
                }
              
                if (!isset($cookie_data['visitor_id'])) {
                    $cookie_data['visitor_id'] = sanitize_text_field($_POST['visitor_id']);
                    $should_update_cookie = true;
                }
            
            }

            if ($should_update_cookie) {
                setcookie("buddybot_session_data", json_encode($cookie_data), time() + $session_lifetime, "/");
            }

            $this->response['success'] = true;
            $this->response['data'] = $cookie_data;
            echo wp_json_encode($this->response);
            wp_die();
        }

        $session_id = bin2hex(random_bytes(16));

        $cookie_data = [
            'session_id' => $session_id,
        ];

        if (isset($_POST['visitor_id'])) {
            $visitor_id = sanitize_text_field($_POST['visitor_id']);

                if (empty($visitor_id) || !$this->is_valid_email_format($visitor_id)) {
                    $this->response['success'] = false;
                    $this->response['message'] = __('Please enter a valid email address.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo wp_json_encode($this->response);
                    wp_die();
                }

            $cookie_data['visitor_id'] = sanitize_text_field($_POST['visitor_id']);
        }

        $encoded_data = json_encode($cookie_data);

        setcookie("buddybot_session_data", $encoded_data, time() + $session_lifetime, "/");

        $this->response['success'] = true;
        $this->response['data'] = $cookie_data;
        $this->visitorEmailPromptHtml();

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function is_valid_email_format($email) {
        return preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email);
    }

    private function visitorEmailPromptHtml()
    {
        $message_html = '
        <div id="buddybot-visitor-id-wrapper" class="d-flex justify-content-center align-items-center">
            <div id="buddybot-visitor-id-card" class="card bg-light mb-3">
                <div class="card-body">
                    <p class="mb-2">' . esc_html(__('To continue, please type your email address below.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</p>
                    <div class="d-flex justify-content-center align-items-center">
                        <input type="email" id="buddybot-visitor-id" name="buddybot-visitor-id" class="form-control form-control-sm me-2" placeholder="' . esc_attr(__('Enter your email', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '" />
                    </div>
                    <div class="d-flex justify-content-end mt-3 align-items-center">
                        <div class="text-danger small me-2 visually-hidden" id="buddybot-visitor-id-error-message"> ' . __('Please enter a valid email address.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>
                        <button id="buddybot-skip-visitor-id" type="button" class="btn btn-sm btn-secondary me-2">' . esc_html(__('Skip', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</button>
                        <button id="buddybot-save-visitor-id-btn" type="button" class="btn btn-sm btn-primary">' . esc_html(__('Submit', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</button>
                    </div>
                </div>
            </div>
        </div>
    ';

    $this->response['html'] = $message_html;
    }


    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getConversationList', array($this, 'getConversationList'));
        add_action('wp_ajax_getMessages', array($this, 'getMessages'));
        add_action('wp_ajax_sendUserMessage', array($this, 'sendUserMessage'));
        add_action('wp_ajax_deleteFrontendThread', array($this, 'deleteFrontendThread'));

        add_action('wp_ajax_nopriv_getConversationList', array($this, 'getConversationList'));
        add_action('wp_ajax_nopriv_getMessages', array($this, 'getMessages'));
        add_action('wp_ajax_nopriv_sendUserMessage', array($this, 'sendUserMessage'));
        add_action('wp_ajax_nopriv_deleteFrontendThread', array($this, 'deleteFrontendThread'));
        add_action('wp_ajax_nopriv_setCookieSession', array($this, 'setCookieSession'));
    }
}