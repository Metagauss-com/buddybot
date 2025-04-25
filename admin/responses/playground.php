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

    public function createRun()
    {
        $this->checkNonce('create_run');
    
        $thread_id = isset($_POST['thread_id']) && !empty($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';
        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';
        
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
                'wp_source' => 'wp_admin',
            ),
            'tool_choice' => array('type' => 'file_search'),
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
    
        $thread_id = isset($_POST['thread_id']) && !empty($_POST['thread_id']) ? sanitize_text_field($_POST['thread_id']) : '';
        $run_id = isset($_POST['run_id']) && !empty($_POST['run_id']) ? sanitize_text_field($_POST['run_id']) : '';
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id . '?include[]=step_details.tool_calls[*].file_search.results[*].content';
    
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
                    $this->tokensMessage();
                    echo wp_json_encode($this->response);
                    wp_die();
                break;
                           
                case 'failed':
                    $this->response['success'] = false;
                    if (isset($output->last_error) && isset($output->last_error->message)) {
                        $this->response['message'] = $output->last_error->message;
                    } else {
                        $this->response['message'] = esc_html__('Sorry, something went wrong.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
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
                        // Translators: %d represents the number of retry attempts.
                        $this->response['message'] = sprintf(esc_html__('Run status still in progress after %d attempts.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), $maxRetries);
                        echo wp_json_encode($this->response);
                        wp_die();
                    }

                    sleep($retryInterval);
                break;

                default:
                    $this->response['success'] = false;
                    // Translators: %s represents the unexpected status message.
                    $this->response['message'] = sprintf(esc_html__('Unexpected status: %s', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($output->status));
                    echo wp_json_encode($this->response);
                    wp_die();
                break;
            }
        }
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

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_createThread', array($this, 'createThread'));
        add_action('wp_ajax_createMessage', array($this, 'createMessage'));
        add_action('wp_ajax_createRun', array($this, 'createRun'));
        add_action('wp_ajax_retrieveRun', array($this, 'retrieveRun'));
        add_action('wp_ajax_listMessages', array($this, 'listMessages'));
        add_action('wp_ajax_deleteThread', array($this, 'deleteThread'));
    }
}