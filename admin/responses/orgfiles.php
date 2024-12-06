<?php

namespace BuddyBot\Admin\Responses;

class OrgFiles extends \BuddyBot\Admin\Responses\MoRoot
{
    
    public function deleteOrgFile()
    {
        $this->checkNonce('delete_org_file');
        $file_id = isset($_POST['file_id']) && !empty($_POST['file_id']) ? sanitize_text_field($_POST['file_id']) : '';
    
        $url = 'https://api.openai.com/v1/files/' . $file_id;
    
        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        $response = wp_remote_request($url, array(
            'method' => 'DELETE',
            'headers' => $headers,
            'timeout' => 60,
        ));
    
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['result'] = $response->get_error_message();
        } else {
            $result = json_decode(wp_remote_retrieve_body($response));
            $this->response['result'] = $result;
    
            if (!empty($result->deleted)) {
                $this->response['success'] = true;
            } else {
                $this->response['success'] = false;
            }
        }
    
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function getOrgFiles()
    {
        $this->checkNonce('get_org_files');
    
        $url = 'https://api.openai.com/v1/files';
    
        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
        );
    
        $response = wp_remote_get($url, array(
            'headers' => $headers,
        ));
    
        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }
    
        $output = json_decode(wp_remote_retrieve_body($response));
        
        if (isset($output->data)) {
            $files = $output->data;
            $this->filesTableHtml($files);
        } else {
            wp_die();
        }
    
        echo wp_kses_post($this->response['html']);
        wp_die();
    }

    private function filesTableHtml($files)
    {
        if (!is_array($files)) {
            return;
        }

        $html = '';

        foreach ($files as $index => $file) {
            $html .= '<tr class="small" data-buddybot-itemid="' . esc_attr($file->id) . '">';
            $html .= '<th scope="row">' . absint($index) + 1 . '</th>';
            $html .= '<td>' . $this->fileIcon($file->filename) . '</td>';
            $html .= '<td>' . esc_html($file->filename) . '</td>';
            $html .= '<td>' . esc_html($file->purpose) . '</td>';
            $html .= '<td>' . esc_html($this->fileSize($file->bytes)) . '</td>';
            $html .= '<td><code>' . esc_html($file->id) . '</code></td>';
            $html .= '<td>' . $this->listBtns('file') . '</td>';
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
        add_action('wp_ajax_deleteOrgFile', array($this, 'deleteOrgFile'));
        add_action('wp_ajax_getOrgFiles', array($this, 'getOrgFiles'));
    }
}