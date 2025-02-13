<?php

namespace BuddyBot\Admin\Responses;

class EditAssistant extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getModels()
    {
        $this->checkNonce('get_models');
    
        if(empty($this->api_key)){
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('To create or edit an assistant, you need to configure your OpenAI API key in the BuddyBot settings.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            $this->response['empty_key'] = true;
            $this->response['html'] = '<option value="" disabled selected>' . esc_html__('No API key configured', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/models';

        $headers = [
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if ($this->openai_response_body->object === 'list') {
            $this->response['success'] = true;
            $this->response['list'] = $this->openai_response_body->data;
            $this->response['html'] = $this->modelsListHtml($this->openai_response_body->data);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to fetch models list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createAssistant()
    {
        $this->checkNonce('create_assistant');
        $this->checkCapabilities();

        $assistant_id = '';

        if (!empty($_POST['assistant_id'])) {
            $assistant_id = '/' . sanitize_text_field($_POST['assistant_id']);
        }

        $vectorstore_id = sanitize_text_field($_POST['vectorstore_id']);

        $url = 'https://api.openai.com/v1/assistants' . $assistant_id;

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $assistant_data = json_decode(wp_unslash(sanitize_text_field($_POST['assistant_data'])), false);
        $instructions = '';

        if (!empty($assistant_data->friendly_name)) {
            $instructions .= " Your name is " . $assistant_data->friendly_name . ". Introduce yourself as " . $assistant_data->friendly_name . " when initiating a conversation.";
        }
        
        if (!empty($assistant_data->aditional_instructions)) {
            $instructions .= $assistant_data->aditional_instructions;
        }
        if (empty($assistant_data->friendly_name) && empty($assistant_data->aditional_instructions)) {
            $instructions .= 'You are a helpful assistant. Answer user queries to the best of your ability.';
        }

        $data = array(
            'model' => $assistant_data->model,
            'name' => $assistant_data->name,
            'description' => $assistant_data->description,
            'instructions' => $instructions,
            "tools" => array(
        array(
            "type" => "file_search"
        )
    ),
            'temperature' => $assistant_data->temperature,
            'top_p' => $assistant_data->top_p,
            'metadata' => array(
                'buddybot_friendly_name' => $assistant_data->friendly_name,
                'buddybot_user_instructions' => $assistant_data->aditional_instructions
            ),
        );

        //if (in_array('file_search', $assistant_data->tools)) {
            if (empty($vectorstore_id)) {
                $this->response['success'] = false;
                $this->response['message'] = esc_html__('Missing AI Training Knowledgebase ID or AI Training Knowledgebase Not Created.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                echo wp_json_encode($this->response);
                wp_die();
    }

                $data['tool_resources'] = array(
                    'file_search' => array(
                        'vector_store_ids' => array($vectorstore_id),
                    ),
                );
          //  }

        $args = [
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'method' => 'POST'
        ];

        $this->openai_response = wp_remote_post($url, $args);
        $this->processResponse();

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function assistantTools($tools)
    {
        $value = array();

        foreach ($tools as $tool) {
            $value[] = array('type' => $tool);
        }

        return $value;
    }

    private function modelsListHtml($list)
    {
        $unsupported_models = $this->config->getProp('unsupported_models');

        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $model) {

            if (!in_array($model->id, $unsupported_models)) {
                $html .= '<option value="' . esc_attr($model->id) . '">';
                $html .= esc_html(strtoupper(str_replace('-', ' ', $model->id)));
                $html .= '</option>';
            }
        }

        return $html;
    }

    public function getAssistantData()
    {
        $this->checkNonce('get_assistant_data');

        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );

        $response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));

        if (is_wp_error($response)) {
            $this->response['sucess'] = false;
            $this->response['message'] = $response->get_error_message();
            wp_die();
        }

        $output = json_decode(wp_remote_retrieve_body($response), true);
        $this->checkError($output);

        $this->response['success'] = true;
        $this->response['result'] = $output;

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getModels', array($this, 'getModels'));
        add_action('wp_ajax_createAssistant', array($this, 'createAssistant'));
        add_action('wp_ajax_getAssistantData', array($this, 'getAssistantData'));
    }
}