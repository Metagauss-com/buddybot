<?php

namespace MetagaussOpenAI\Admin\Responses;

class Assistants extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    
    public function deleteAssistant()
    {
        $this->checkNonce('delete_assistant');
        $file_id = $_POST['file_id'];

        $url = 'https://api.openai.com/v1/files/' . $file_id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $this->response['result'] = json_decode(curl_exec($ch));
        
        if ($this->response['result']->deleted) {
            $this->response['success'] = true;
        } else {
            $this->response['success'] = false;
        }

        echo json_encode($this->response);
        wp_die();
    }

    public function getAssistants()
    {
        $this->checkNonce('get_assistants');

        $url = 'https://api.openai.com/v1/assistants';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $this->response['result'] = json_decode(curl_exec($ch));
        if ($this->response['result']->object === 'list') {
            $this->response['success'] = true;
            $this->assistantsTableHtml();
        } else {
            $this->response['success'] = false;
        }
        
        curl_close($ch);

        echo wp_kses_post($this->response['html']);
        wp_die();
    }

    private function assistantsTableHtml()
    {
        if (!is_array($this->response['result']->data)) {
            return;
        }

        $html = '';

        foreach ($this->response['result']->data as $index => $assistant) {
            $html .= '<tr class="small" data-mo-itemid="' . esc_attr($assistant->id) . '">';
            $html .= '<th scope="row">' . absint($index) + 1 . '</th>';
            $html .= '<td>' . esc_html($assistant->name) . '</td>';
            $html .= '<td>' . esc_html($assistant->description) . '</td>';
            $html .= '<td>' . esc_html($assistant->model) . '</td>';
            $html .= '<td><code>' . esc_html($assistant->id) . '</code></td>';
            $html .= '<td>' . $this->listBtns('assistant') . '</td>';
            $html .= '</tr>';
        }

        $this->response['html'] = $html;
    }

    private function fileIcon($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $icon_dir = $this->config->getRootPath() . 'admin/html/images/fileicons/';
        $icon_file = $icon_dir . $ext . '.png';
        $icon_url = $this->config->getRootUrl() . 'admin/html/images/fileicons/';

        if (file_exists($icon_file)) {
            $icon_url = $icon_url . $ext . '.png';
        } else {
            $icon_url = $icon_url . 'file.png';
        }

        return '<img width="16" src="' . $icon_url . '">';

    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_deleteAssistant', array($this, 'deleteAssistant'));
        add_action('wp_ajax_getAssistants', array($this, 'getAssistants'));
    }
}