<?php

namespace BuddyBot\Admin\Responses;

class Settings extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getOptions()
    {
        $this->checkNonce('get_options');

        $section = sanitize_text_field($_POST['section']);
        $section_class = '\BuddyBot\Admin\Html\Views\Settings\\' . $section;
        $selection_object = new $section_class();
        $this->response['success'] = true;
        $this->response['html'] = $selection_object->getHtml();
        print_r($this->response);
        wp_die();
    }

    public function saveSettings()
    {
        $this->checkNonce('save_settings');

        $options_data = json_decode(wp_unslash(sanitize_text_field($_POST['options_data'])), true);

        if (!is_array($options_data)) {
            $this->response['success'] = false;
            $this->response['message'] = array(__('Invalid data.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
            $this->response['errors'] = array(__('Data must be in array format.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
            echo wp_json_encode($this->response);
            wp_die();
        }

        $secure_class = '\BuddyBot\Admin\Secure\Settings\\' . sanitize_text_field($_POST['section']);
        $secure = new $secure_class();
        $options = $secure->secureData($options_data);
        $errors = $secure->getErrors();

        if (count($errors) > 0) {
            $this->response['success'] = false;
            $this->response['message'] = array(__('There was a problem with options data.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
            $this->response['errors'] = $errors;
            echo wp_json_encode($this->response);
            wp_die();
        }

        foreach ($options as $option_name => $option_value) {
            $this->sql->saveOption($option_name, $option_value);
            if ($option_name === 'delete_expired_chat') {
                if ($option_value == 1) {
                    if (!wp_next_scheduled('buddybot_delete_expired_chats')) {
                        wp_schedule_event(time(), 'daily', 'buddybot_delete_expired_chats');
                    }
                } else {
                    $timestamp = wp_next_scheduled('buddybot_delete_expired_chats');
                    if ($timestamp) {
                        wp_unschedule_event($timestamp, 'buddybot_delete_expired_chats');
                    }
                }
            }
        }

        $this->response['success'] = true;
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function verifyApiKey()
    {
        $this->checkNonce('verify_api_key');

        $api_key = isset($_POST['api_key']) && !empty($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';

        $url = 'https://api.openai.com/v1/models';

        $headers = [
            'Authorization' => 'Bearer ' . $api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if (!$this->openai_response_body->object === 'list') {
            $this->response['success'] = false;
            $this->response['message'] = __('Incorrect Api Key.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function autoCreateVectorStore()
    {
        $this->checkNonce('auto_create_vectorstore');
        $this->checkCapabilities();
        $api_key = isset($_POST['api_key']) && !empty($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';

        $url = 'https://api.openai.com/v1/vector_stores';

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $api_key
        ];

        $vectorstore_data = json_decode(wp_unslash(sanitize_text_field($_POST['vectorstore_data'])), false);

        $data = array(
            'name' => $vectorstore_data->name,
        );

        $args = [
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'method' => 'POST'
        ];

        $this->openai_response = wp_remote_post($url, $args);
        $this->processResponse();

        if ($this->response['success'] == true) {
            $response_body = json_decode(wp_remote_retrieve_body($this->openai_response), true);

            if (isset($response_body['id']) && isset($response_body['name'])) {

                $vectorstore_data = [
                    'name' => $response_body['name'],
                    'id' => $response_body['id']
                ];
                update_option('buddybot_vectorstore_data', $vectorstore_data);
            }
        }
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function checkVectorStore()
    {
        $this->checkNonce('auto_create_vectorstore');

        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';
        $api_key = isset($_POST['api_key']) && !empty($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';

        $url = 'https://api.openai.com/v1/vector_stores/' . $vectorstore_id;

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );

        $this->openai_response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));
       
        $this->processResponse();

        if (isset($this->openai_response_body->id) && !empty($this->openai_response_body->id)) {
            $this->response['success'] = true;
        } else {
            $this->response['success'] = false;
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function checkAllVectorStore()
    {
        $this->checkNonce('auto_create_vectorstore');
        $api_key = isset($_POST['api_key']) && !empty($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';

        $url = 'https://api.openai.com/v1/vector_stores?limit=10';

        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if ($this->openai_response_body->object === 'list') {
            $this->response = $this->matchVectorDomain();
        } else {
            $this->response['success'] = false;
            $this->response['create_vectorstore'] = true;
            $this->response['message'] = esc_html__('Unable to fetch VectorStore list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function matchVectorDomain()
    {
        if (!is_array($this->openai_response_body->data)) {
            return [
                'success' => false,
                'create_vectorstore' => true,
                'message' => esc_html__('Output is not an Array.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            ];
        }

        $hostname = wp_parse_url(home_url(), PHP_URL_HOST);

        if($hostname === 'localhost'){
            $path = wp_parse_url(home_url(), PHP_URL_PATH) ?? '';
            $hostname = $hostname . str_replace('/', '.', $path); 
        }

        $vectorstore_name = '';
        $vectorstore_id = '';
        foreach ($this->openai_response_body->data as $store) {
            if (isset($store->name, $store->id) && $store->name === $hostname) {
                $vectorstore_id = $store->id;
                $vectorstore_name = $store->name;
                break;
            }
        }

        if(!empty($vectorstore_id) && !empty($vectorstore_name)) {
            $vectorstore_data = [
                'name' => $vectorstore_name,
                'id' => $vectorstore_id
            ];
            update_option('buddybot_vectorstore_data', $vectorstore_data);
            return [
                'success' => true,
                'data'    => $vectorstore_data,
            ];
        } else {
            return [
                'success' => false,
                'create_vectorstore' => true,
                'message' => esc_html__('No matching VectorStore found.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            ];
        }
    }

    public function DeleteExpiredChats()
    {
        $expiry_hours = $this->sql->getOption('session_expiry', 24);
        $expired_threads = $this->sql->getExpiredThreads($expiry_hours);
        if (!empty($expired_threads)) {
            foreach ($expired_threads as $thread_id) {
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

                    $this->sql->addLogEntry(
                        'system',
                        'failure',
                        __('OpenAI API request failed: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . $response->get_error_message(),
                        json_encode(['thread_id' => $thread_id])
                    );
                    continue;
                }

                $output = json_decode(wp_remote_retrieve_body($response));

                $notFound = false;
                if (isset($output->error) && isset($output->error->message)) {
                    if (strpos(strtolower($output->error->message), 'no thread found') !== false) {
                        $notFound = true;
                    }
                }
                if (!$notFound) {
                    if (!empty($output->error)) {
                        $this->sql->addLogEntry(
                            'system',
                            'failure',
                            __('There was an error:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . $output->error->message,
                            json_encode(['thread_id' => $thread_id, 'response' => $output])
                        );
                        continue;
                    }
                }

                if (isset($output->deleted) && $output->deleted || $notFound) {
                    $response = $this->sql->deleteExpiredThread($thread_id);
                    if ($response === false) {
                        global $wpdb;
                        $this->sql->addLogEntry(
                            'system',
                            'failure',
                            __('Database error while deleting thread: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . $wpdb->last_error,
                            json_encode(['thread_id' => $thread_id])
                        );
                    }
                }
            }
        }
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getOptions', array($this, 'getOptions'));
        add_action('wp_ajax_saveSettings', array($this, 'saveSettings'));
        add_action('wp_ajax_verifyApiKey', array($this, 'verifyApiKey'));
        add_action('wp_ajax_checkVectorStore', array($this, 'checkVectorStore'));
        add_action('wp_ajax_checkAllVectorStore', array($this, 'checkAllVectorStore'));
        add_action('wp_ajax_autoCreateVectorStore', array($this, 'autoCreateVectorStore'));
        add_action('buddybot_delete_expired_chats', array($this, 'DeleteExpiredChats'));
    }
}