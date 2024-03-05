<?php

namespace MetagaussOpenAI\Admin\Responses;

class AddFile extends \MetagaussOpenAI\Admin\Responses\MoRoot
{

    public function addFile()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'add_file');

        if ($nonce_status === false) {
            wp_die();
        }

        $file = $_POST['file'];
        $cfile = curl_file_create($file, mime_content_type($file), 'ewfwef');

        $url = 'https://api.openai.com/v1/files';
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $data = array(
            'purpose' => 'assistants',
            'file' => $cfile
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = json_decode(curl_exec($ch));
        print_r($output);
        curl_close($ch);

        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_addFile', array($this, 'addFile'));
    }
}