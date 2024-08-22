<?php

namespace BuddyBot\Admin\Responses;

class EditAssistant extends \BuddyBot\Admin\Responses\MoRoot
{
    public function getModels()
    {
        $this->checkNonce('get_models');

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
            $this->response['message'] = __('Unable to fetch models list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function getFiles()
    {
        $this->checkNonce('get_files');

        $url = 'https://api.openai.com/v1/files';
        
        $headers = ['Authorization' => 'Bearer ' . $this->api_key];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        $this->response['html'] = $this->filesListHtml($this->openai_response_body->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createAssistant()
    {
        $this->checkNonce('create_assistant');
        $this->checkCapabilities();

        $assistant_id = '';

        if (!empty(sanitize_text_field($_POST['assistant_id']))) {
            $assistant_id = '/' . sanitize_text_field($_POST['assistant_id']);
        }

        $url = 'https://api.openai.com/v1/assistants' . $assistant_id;
        
        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $assistant_data = json_decode(wp_unslash($_POST['assistant_data']));

        $data = array(
            'model' => $assistant_data->model,
            'name' => $assistant_data->name,
            'description' => $assistant_data->description,
            'tools' => $this->assistantTools($assistant_data->tools),
            'file_ids' => $this->assistantFiles($assistant_data->file_ids)
        );

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

    private function assistantFiles($file_ids)
    {
        $value = array();

        foreach ($file_ids as $file_id) {
            $value[] = $file_id;
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

    private function filesListHtml($list)
    {
        $remote_posts_file_id = $this->core_files->getRemoteFileId('posts');
        $remote_comments_file_id = $this->core_files->getRemoteFileId('comments');

        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $file) {

            $ext = pathinfo($file->filename, PATHINFO_EXTENSION);

            if (empty($ext)) {
                continue;
            }

            $badge ='';

            if ($file->id === $remote_posts_file_id) {
                $badge = '<span class="badge text-bg-success rounded-pill ms-1 text-uppercase">' . __('Latest Posts File', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
            }

            if ($file->id === $remote_comments_file_id) {
                $badge = '<span class="badge text-bg-success rounded-pill ms-1 text-uppercase">' . __('Latest Comments File', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
            }

            if ($file->purpose === 'assistants' and absint($file->bytes) <= 536870912) {
                $html .= '<div class="mb-2">';
                $html .= '<label for="' . $file->id . '" title="' . $file->filename . '">';
                $html .= '<input type="checkbox" class="me-2 buddybot-item-field" id="' . $file->id . '" value="' . $file->id . '">';
                $html .= (strlen($file->filename) > 20) ? substr($file->filename, 0, 20).'...' : $file->filename;
                $html .= '<span class="font-monospace ms-1 text-muted"> / ' . $ext . '</span>';
                $html .= '<span class="font-monospace ms-1 text-muted"> / ' . $this->fileSize($file->bytes) . '</span>';
                $html .= $badge;
                $html .= '</label>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function getAssistantData()
    {
        $this->checkNonce('get_assistant_data');

        $assistant_id = sanitize_text_field($_POST['assistant_id']);

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
            )
        );

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getModels', array($this, 'getModels'));
        add_action('wp_ajax_getFiles', array($this, 'getFiles'));
        add_action('wp_ajax_createAssistant', array($this, 'createAssistant'));
        add_action('wp_ajax_getAssistantData', array($this, 'getAssistantData'));
    }
}