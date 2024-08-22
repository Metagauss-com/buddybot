<?php

namespace BuddyBot\Admin\Responses;

class AddFile extends \BuddyBot\Admin\Responses\MoRoot
{

    public function addFile()
    {
        $nonce_status = wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'add_file');

        if ($nonce_status === false) {
            wp_die();
        }

        $file_id = sanitize_text_field($_POST['file_id']);

        $cfile = curl_file_create(
            wp_get_attachment_url($file_id),
            get_post_mime_type($file_id),
            get_the_title($file_id)
        );

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

        $output = curl_exec($ch);

        if ($output != false) {
            $response['success'] = true;
            $output = json_decode($output);
            $response['html'] = $this->printFileOutput($output);
        } else {
            $response['success'] = false;
        }

        echo wp_json_encode($response);
        curl_close($ch);

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