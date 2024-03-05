<?php

namespace MetagaussOpenAI\Admin\Responses;

class OrgFiles extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function getOrgFiles()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'get_org_files');

        if ($nonce_status === false) {
            wp_die();
        }

        $url = 'https://api.openai.com/v1/files';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = json_decode(curl_exec($ch));
        $files = $output->data;
        $this->filesTableHtml($files);
        curl_close($ch);

        print_r($this->response['html']);
        wp_die();
    }

    private function filesTableHtml($files)
    {
        if (!is_array($files)) {
            return;
        }

        $html = '';

        foreach ($files as $index => $file) {
            $html .= '<tr class="small">';
            $html .= '<th scope="row">' . absint($index) + 1 . '</th>';
            $html .= '<td>' . $this->fileIcon($file->filename) . '</td>';
            $html .= '<td>' . esc_html($file->filename) . '</td>';
            $html .= '<td>' . esc_html($file->purpose) . '</td>';
            $html .= '<td>' . esc_html($this->fileSize($file->bytes)) . '</td>';
            $html .= '<td><code>' . esc_html($file->id) . '</code></td>';
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

    private function fileSize($bytes)
    {

        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getOrgFiles', array($this, 'getOrgFiles'));
    }
}