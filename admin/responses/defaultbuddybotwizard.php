<?php

namespace BuddyBot\Admin\Responses;

class DefaultBuddyBotWizard extends \BuddyBot\Admin\Responses\MoRoot
{
    protected $file_data = '';

    public function isLocalFileWritable()
    {
        $this->checkNonce('is_local_file_writable');
        $data_types = $_POST['data_types'];

        if (!is_array($data_types)) {
            $data_types = array();
            $this->response['success'] = false;
            $this->response['message'] = __('Data types should be passed as an array.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        $errors = 0;

        foreach ($data_types as $data_type) {
            
            $file = $this->core_files->getLocalPath(sanitize_text_field($data_type));
            
            if ($file === false) {
                $errors += 1;
                // Translators: %s is the type of data which is being synced with OpenAI server.
                $this->response['message'] .= sprintf(esc_html__('%s file path is not defined.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($data_type));
            }

            if (!is_writable($file)) {
                $errors += 1;
                // Translators: %s is the type of data which is being synced with OpenAI server.
                $this->response['message'] .= sprintf(esc_html__('%s file is not writable.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($data_type));
            }

        }

        if ($errors === 0) {
            $this->response['success'] = true;
            $this->response['message'] = __('Yay! The files are writable.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->response['success'] = false;
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function addDataToFile()
    {
        $this->checkNonce('add_data_to_file');
        $this->checkCapabilities();
        
        $data_type = sanitize_text_field($_POST['data_type']);

        $method = 'compile' . $data_type;

        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Data compile method undefined. Operation aborted.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            echo wp_json_encode($this->response);
            wp_die();
        }

        $this->writeData($data_type);
        
        $this->response['success'] = true;
        $this->response['message'] = '<div>' . __('Added data to file.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function compilePosts()
    {
        $args = array(
            'post_type' => 'post'
        );
    
        $post_query = new \WP_Query($args);
    
        if($post_query->have_posts()) {
            while($post_query->have_posts()) {
                $post_query->the_post();
                $this->file_data .= wp_strip_all_tags(get_the_title());
                $this->file_data .= wp_strip_all_tags(get_the_content());
            }
        }

        wp_reset_postdata();
    }

    function compileComments()
    {
        $args = array(
            'status' => 'approve' // Fetch only approved comments
        );

        $comments = get_comments($args);

        foreach ($comments as $comment) {
            $this->file_data .= wp_strip_all_tags($comment->comment_content);
        }
    
    }

    private function writeData($data_type)
    {
        $file = fopen($this->core_files->getLocalPath($data_type), "w");
        fwrite($file, str_replace('&nbsp;',' ', $this->file_data));
        fclose($file);
        $this->file_data = '';
    }

    private function updateRemoteFileOption($data_type, $output)
    {
        $update = update_option($this->core_files->getWpOptionName($data_type), $output->id, false);

        if ($update) {
            $this->response['success'] = true;
            // Translators: %s is the remote ID of the recently uploaded file on the OpenAI server.
            $this->response['message'] = '<div>' . sprintf(esc_html__('Remote file name updated to %s.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($output->id)) . '</div>';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Unable to update remote file name.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        }

        echo wp_json_encode($this->response);
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_isLocalFileWritable', array($this, 'isLocalFileWritable'));
    }
}