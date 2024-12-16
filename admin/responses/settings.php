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

    public function createVectorStore()
    {
        $this->checkNonce('create_vectorstore');
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


    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getOptions', array($this, 'getOptions'));
        add_action('wp_ajax_saveSettings', array($this, 'saveSettings'));
        add_action('wp_ajax_verifyApiKey', array($this, 'verifyApiKey'));
        add_action('wp_ajax_createVectorStore', array($this, 'createVectorStore'));
    }
}