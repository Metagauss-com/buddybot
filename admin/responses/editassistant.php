<?php

namespace MetagaussOpenAI\Admin\Responses;

class EditAssistant extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function getModels()
    {
        $this->checkNonce('get_models');

        $url = 'https://api.openai.com/v1/models';
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = curl_exec($ch);
        curl_close($ch);

        $output = json_decode($output);

        if ($output->object === 'list') {
            $this->response['success'] = true;
            $this->response['list'] = $output->data;
            $this->response['html'] = $this->modelsListHtml($output->data);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to fetch models list.', 'metagauss-openai');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function getFiles()
    {
        $this->checkNonce('get_files');

        $url = 'https://api.openai.com/v1/files';
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        $this->response['html'] = $this->filesListHtml($output->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createAssistant()
    {
        $this->checkNonce('create_assistant');
        $this->checkCapabilities();

        $url = 'https://api.openai.com/v1/assistants';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
            )
        );

        $assistant_data = json_decode(wp_unslash($_POST['assistant_data']));

        $data = array(
            'model' => $assistant_data->model,
            'name' => $assistant_data->name,
            'description' => $assistant_data->name,
        );

        $data['tools'] = $this->assistantTools($assistant_data->tools);
        $data['file_ids'] = $this->assistantFiles($assistant_data->file_ids);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        $this->response['result'] = $output;
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
        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $model) {
            $html .= '<option value="' . esc_attr($model->id) . '">';
            $html .= esc_html($model->id);
            $html .= '</option>';
        }

        return $html;
    }

    private function filesListHtml($list)
    {
        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $file) {

            if ($file->purpose === 'assistants') {
                $html .= '<div class="mb-2 text-muted">';
                $html .= '<label for="' . $file->id . '">';
                $html .= '<input type="checkbox" id="' . $file->id . '" value="' . $file->id . '">';
                $html .= $file->filename;
                $html .= '</label>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getModels', array($this, 'getModels'));
        add_action('wp_ajax_getFiles', array($this, 'getFiles'));
        add_action('wp_ajax_createAssistant', array($this, 'createAssistant'));
    }
}