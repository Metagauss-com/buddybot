<?php

namespace BuddyBot\Admin\Responses;

class AddFile extends \BuddyBot\Admin\Responses\MoRoot
{
    
    public function addFile()
    {
        $this->checkNonce('add_file');

        $file_id = sanitize_text_field($_POST['file_id']);

        $file_url = wp_get_attachment_url($file_id);
        $file_mime_type = get_post_mime_type($file_id);
        $file_name = get_the_title($file_id);
        
        // Download the file to the server's temporary directory
        $temp_file = download_url($file_url);
        
        if (is_wp_error($temp_file)) {
            $this->response['success'] = false;
            echo wp_json_encode($this->response);
            wp_die();
        }

        $file_array = array(
            'name' => $file_name,
            'type' => $file_mime_type,
            'tmp_name' => $temp_file,
            'error' => 0,
            'size' => filesize($temp_file),
        );

        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
        );

        $data = array(
            'purpose' => 'assistants',
            'file' => $file_array,
        );

        $response = wp_remote_post('https://api.openai.com/v1/files', array(
            'headers' => $headers,
            'body' => $data,
            'timeout' => 60,
            'httpversion' => '1.0',
            'blocking' => true,
            'cookies' => array(),
            'sslverify' => false,
        ));

        // Clean up the temporary file
        wp_delete_file($temp_file);

        if (is_wp_error($response)) {
            $response_data['success'] = false;
        } else {
            $response_body = wp_remote_retrieve_body($response);
            $output = json_decode($response_body);

            if ($output) {
                $response_data['success'] = true;
                $response_data['html'] = $this->printFileOutput($output);
            } else {
                $response_data['success'] = false;
            }
        }

        echo wp_json_encode($response_data);
        wp_die();
    }

    private function printFileOutput($output)
    {
        $html = '<span>';
        // Translators: %s is the remote file ID from OpenAI server.
        $html .= sprintf(esc_html__('Your file has been uploaded successfully with id <b>%s</b>', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), $output->id);
        $html .= '</span>';
        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_addFile', array($this, 'addFile'));
    }
}