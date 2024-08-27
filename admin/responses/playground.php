<?php

namespace BuddyBot\Admin\Responses;

class Playground extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getAssistantOptions()
    {
        $this->checkNonce('get_assistants');
    
        $url = 'https://api.openai.com/v1/assistants?limit=50';
    
        $headers = array(
            'OpenAI-Beta' => 'assistants=v1',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        $response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));
    
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            wp_die();
        }
    
        $output = json_decode(wp_remote_retrieve_body($response));
        $this->checkError($output);
    
        if (isset($output->object) && $output->object === 'list') {
            $this->response['success'] = true;
            $this->assistantOptionsHtml($output);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to fetch assistants list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        echo wp_json_encode($this->response);
        wp_die();
    }

    protected function assistantOptionsHtml($assistants)
    {
        $this->response['html'] = '';

        if (!is_array($assistants->data)) {
            return;
        }

        foreach ($assistants->data as $assistant) {
            $name = $assistant->name;
            $id = $assistant->id;
            $model = $assistant->model;

            if (empty($name)) {
                $name = $assistant->id;
            }

            $this->response['html'] .= '<option value="' . $id . '">' . $name . ' (' . $model . ')</option>';
        }
    }

    public function createThread()
    {
        $this->checkNonce('create_thread');
    
        $url = 'https://api.openai.com/v1/threads';
    
        $headers = array(
            'OpenAI-Beta' => 'assistants=v1',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        $data = array(
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
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
                $this->response['message'] = __('Unable to save thread in the database', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createMessage()
    {
        $this->checkNonce('create_message');

        $thread_id = sanitize_text_field($_POST['thread_id']);
        $message = sanitize_textarea_field(wp_unslash($_POST['message']));
        $file_url = sanitize_text_field($_POST['file_url']);
        $file_mime = sanitize_text_field($_POST['file_mime']);

        $file_id = '';

        if (filter_var($file_url, FILTER_VALIDATE_URL)) {
            $file_id = $this->uploadMessageFile($file_url, $file_mime);
        }

        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v1',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );

        // Prepare the data
        $data = array(
            'role' => 'user',
            'content' => $message,
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'wp_admin',
            ),
        );

        if (!empty($file_id)) {
            $data['file_ids'] = array($file_id);
        }

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


    private function uploadMessageFile($file_url, $file_mime)
    {
        // Download the file temporarily
        $temp_file = download_url($file_url);
    
        if (is_wp_error($temp_file)) {
            // Handle the error if the download fails
            $this->response['success'] = false;
            $this->response['message'] .= $temp_file->get_error_message();
            return '';
        }
    
        // Prepare the boundary string
        $boundary = wp_generate_password(24);
        $eol = "\r\n";
    
        // Read the content of the temporary file
        $file_content = file_get_contents($temp_file);
    
        // Prepare the body with multipart/form-data
        $body = '';
        $body .= '--' . $boundary . $eol;
        $body .= 'Content-Disposition: form-data; name="purpose"' . $eol . $eol;
        $body .= 'assistants' . $eol;
    
        $body .= '--' . $boundary . $eol;
        $body .= 'Content-Disposition: form-data; name="file"; filename="' . basename($file_url) . '"' . $eol;
        $body .= 'Content-Type: ' . $file_mime . $eol . $eol;
        $body .= $file_content . $eol;
        $body .= '--' . $boundary . '--' . $eol;
    
        // Set up the headers for the request
        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'multipart/form-data; boundary=' . $boundary,
        );
    
        // Perform the POST request
        $response = wp_remote_post('https://api.openai.com/v1/files', array(
            'headers' => $headers,
            'body'    => $body,
            'timeout' => 60,
        ));
    
        // Clean up the temporary file
        wp_delete_file($temp_file);
    
        // Check for errors
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] .= $response->get_error_message();
            return '';
        }
    
        // Decode the response body
        $output = json_decode(wp_remote_retrieve_body($response));
    
        // Check for errors in the output
        $this->checkError($output);
    
        return $output->id;
    }
    

    public function createRun()
    {
        $this->checkNonce('create_run');
    
        $thread_id = sanitize_text_field($_POST['thread_id']);
        $assistant_id = sanitize_text_field($_POST['assistant_id']);
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';
    
        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v1',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        // Prepare the data
        $data = array(
            'assistant_id' => $assistant_id,
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
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
    
        echo wp_json_encode($this->response);
        wp_die();
    }
    

    public function retrieveRun()
    {
        $this->checkNonce('retrieve_run');
    
        $thread_id = sanitize_text_field($_POST['thread_id']);
        $run_id = sanitize_text_field($_POST['run_id']);
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;
    
        // Prepare the headers
        $headers = array(
            'OpenAI-Beta' => 'assistants=v1',
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
    
        $this->tokensMessage();
    
        echo wp_json_encode($this->response);
        wp_die();
    }
    
    

    private function tokensMessage()
    {
        if ($this->response['result']->status !== "completed") {
            return;
        }

        $prompt_tokens = absint($this->response['result']->usage->prompt_tokens);
        $completion_tokens = absint($this->response['result']->usage->completion_tokens);
        $total_tokens = absint($this->response['result']->usage->total_tokens);

        // Translators: %1d are number of tokens used by user prompt. %2d is number of tokens used in completition run. %3d are total tokens used in the run.
        $message = sprintf(esc_html__('Tokens Prompt: %1$1d. Completion: %2$2d. Total: %3$3d.','buddybot-ai-custom-ai-assistant-and-chat-agent'),
                absint($prompt_tokens),
                absint($completion_tokens),
                absint($total_tokens)
            );

        $this->response['tokens'] = $message;
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
            'OpenAI-Beta' => 'assistants=v1',
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
            'OpenAI-Beta' => 'assistants=v1'
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
        add_action('wp_ajax_getAssistantOptions', array($this, 'getAssistantOptions'));
        add_action('wp_ajax_createThread', array($this, 'createThread'));
        add_action('wp_ajax_createMessage', array($this, 'createMessage'));
        add_action('wp_ajax_createRun', array($this, 'createRun'));
        add_action('wp_ajax_retrieveRun', array($this, 'retrieveRun'));
        add_action('wp_ajax_listMessages', array($this, 'listMessages'));
        add_action('wp_ajax_deleteThread', array($this, 'deleteThread'));
    }
}