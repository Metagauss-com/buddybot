<?php

namespace BuddyBot\Admin\Responses;

class Playground extends \BuddyBot\Admin\Responses\MoRoot
{

    public function createThread()
    {
        $this->checkNonce('create_thread');
    
        $url = 'https://api.openai.com/v1/threads';
    
        $headers = array(
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        $data = array(
            'metadata' => array(
                'wp_user_id' => (string)get_current_user_id(),
                'wp_source' => 'wp_admin'
            )
        );
    
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body'    => wp_json_encode($data),
            'timeout' => 60,
        ));
    
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            wp_die();
        }
    
        $output = json_decode(wp_remote_retrieve_body($response));    
        $this->checkError($output);
    
        if ($this->response['success']) {
            $insert = $this->sql->saveThreadId($output->id);
            if ($insert === false) {
                $this->response['success'] = false;
                $this->response['message'] = esc_html__('Unable to save thread in the database', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createMessage()
    {
        $this->checkNonce('create_message');

        $thread_id = isset($_POST['thread_id']) && !empty($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';
        $message = isset($_POST['message']) && !empty($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

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
            'content' => $message,
            'metadata' => array(
                'wp_user_id' => (string)get_current_user_id(),
                'wp_source' => 'wp_admin',
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
            wp_die();
        }

        // Decode the response body
        $output = json_decode(wp_remote_retrieve_body($response));

        // Check for errors in the output
        $this->checkError($output);

        $this->sql->updateThreadName($thread_id, $message);

        $this->response['html'] = $this->chatBubbleHtml($output);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function listMessages()
    {
        $this->checkNonce('list_messages');
    
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
        $chat_bubble = new \BuddyBot\Admin\Html\Elements\Playground\ChatBubble();
        $chat_bubble->setMessage($message);
        return $chat_bubble->getHtml();
    }

    public function deleteThread()
    {
        $this->checkNonce('delete_thread');
        $thread_id = sanitize_text_field($_POST['thread_id']);
    
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
            $this->sql->deleteThread($thread_id);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to delete conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function buddybotStream()
    {
        $this->checkNonce('buddybot_stream');

        $thread_id = sanitize_text_field($_POST['threadId'] ?? '');
        $assistant_id = sanitize_text_field($_POST['assistantId'] ?? '');

        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';

        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'OpenAI-Beta: assistants=v2',
        ];

        $postData = json_encode([
            'assistant_id' => $assistant_id,
            'stream' => true,
            'metadata' => [
                'wp_user_id' => (string)get_current_user_id(),
                'wp_source' => 'wp_admin',
            ],
        ]);

        // Proper headers for plain text line streaming
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable buffering for NGINX

        if (ob_get_level()) {
            ob_end_clean();
        }

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', 'off');
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        flush();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) {
            echo $data; // Raw data (one line of JSON or a chunk)
            flush();
            return strlen($data);
        });

        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

        // Only use these for testing/dev environments
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
            $errorData = json_encode([
                'error' => [
                    'message' => $error,
                    'type' => 'curl_error',
                ],
            ]);
            echo $errorData . "\n";
            flush();
        }

        curl_close($ch);
        exit;
    }

      public function loadThreads()
    {
        $this->checkNonce('load_threads');
    
        $offset = (isset($_POST['offset']) && !empty($_POST['offset'])) ? absint($_POST['offset']) : 0;
        $search = (isset($_POST['search']) && !empty($_POST['search'])) ? sanitize_text_field($_POST['search']) : '';

        $response = $this->sql->getThreadsByUserId(0, 10, $offset, $search);
        //print_r($response);die;
    
        if ($response['success']) {
            $this->response['success'] = true;
            $this->response['html'] = $this->threadsHtml($response['result']);
            $this->response['has_more'] = !empty($response['result']);

        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to load more threads.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        echo wp_json_encode($this->response);
        wp_die();
    }

    private function threadsHtml($threads)
    {
        if (empty($threads)) {
            return '<div class="buddybot-no-threads-message p-2 text-muted small">No More threads available.</div>';
        }

        $html = '';
        foreach ($threads as $thread) {
             $label = $thread->thread_name;

            if (empty($label)) {
                $label = $thread->thread_id;
            }

            error_log($thread->thread_name);

           // $date = get_date_from_gmt($thread->created, 'Y-m-d');
            $html .= '<div class="buddybot-threads-container buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-me-3 buddybot-p-1 buddybot-text-small buddybot-text-decoration-none" >';
            $html .= '<div class="buddybot-threads-list-item buddybot-d-flex buddybot-align-items-center  buddybot-text-truncate buddybot-me-3 buddybot-pt-2" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            $html .= '<span class="buddybot-threads-icon buddybot-me-2 buddybot-ms-1 "><svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="#1f1f1f"><path d="M323.79-516q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm156 0q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm156 0q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5ZM96-96v-696q0-29.7 21.15-50.85Q138.3-864 168-864h624q29.7 0 50.85 21.15Q864-821.7 864-792v480q0 29.7-21.15 50.85Q821.7-240 792-240H240L96-96Zm114-216h582v-480H168v522l42-42Zm-42 0v-480 480Z"/></svg></span>';
            $html .= '<div class="buddybot-threads-list-item-text buddybot-text-truncate buddybot-text-small buddybot-mb-2">';
            $html .= esc_html($label);
            $html .= '</div>'; 
           // $html .=  esc_html($date);    
            $html .= '</div>';
            $html .= '<div class="buddybot-thread-delete buddybot-ms-1 buddybot-me-3 buddybot-mt-1 buddybot-d-none" style="cursor:pointer" data-modal="buddybot-del-conversation-modal">   
          <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#f1312e"><path d="M312-144q-29.7 0-50.85-21.15Q240-186.3 240-216v-480h-48v-72h192v-48h192v48h192v72h-48v479.57Q720-186 698.85-165T648-144H312Zm336-552H312v480h336v-480ZM384-288h72v-336h-72v336Zm120 0h72v-336h-72v336ZM312-696v480-480Z"/></svg>
             </div>';
            $html .= '</div>';
        }

        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_createThread', array($this, 'createThread'));
        add_action('wp_ajax_createMessage', array($this, 'createMessage'));
        add_action('wp_ajax_listMessages', array($this, 'listMessages'));
        add_action('wp_ajax_deleteThread', array($this, 'deleteThread'));
        add_action('wp_ajax_buddybotStream', array($this, 'buddybotStream'));
        add_action('wp_ajax_loadThreads', array($this, 'loadThreads'));
    }
}