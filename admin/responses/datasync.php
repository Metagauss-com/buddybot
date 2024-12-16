<?php

namespace BuddyBot\Admin\Responses;

class DataSync extends \BuddyBot\Admin\Responses\MoRoot
{
    protected $file_data = '';

    public function checkFileStatus()
    {
        $this->checkNonce('check_file_status');
        
        $file_id = isset($_POST['file_id']) && !empty($_POST['file_id']) ? sanitize_text_field($_POST['file_id']) : '';

        $url = 'https://api.openai.com/v1/files/' . sanitize_text_field($file_id);
        
        $headers = [
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();
        
        $this->response['message'] = esc_html__('File syncronized!', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function isBbFileWritable()
    {
        $this->checkNonce('is_file_writable');
        $data_type = isset($_POST['data_type']) && !empty($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : '';
        $file = $this->core_files->getLocalPath($data_type);

        WP_Filesystem();
        global $wp_filesystem;

        if ($wp_filesystem->is_writable($file)) {
            $this->response['success'] = true;
            $this->response['message'] = '<div class="text-success">' . esc_html__('The file is writable.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div class="text-danger">' . esc_html__('The file is not writable.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function addDataToFile()
    {
        $this->checkNonce('add_data_to_file');
        $this->checkCapabilities();
        
        $data_type = isset($_POST['data_type']) && !empty($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : '';

        $method = 'compile' . $data_type;

        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div class="text-danger">' . esc_html__('Data compile method undefined. Operation aborted.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            echo wp_json_encode($this->response);
            wp_die();
        }

        $this->writeData($data_type);
        
        $this->response['success'] = true;
        $this->response['message'] = '<div class="text-success">' . esc_html__('Added data to file.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';

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

/*     private function writeData($data_type)
    {
        $file = fopen($this->core_files->getLocalPath($data_type), "w");
        fwrite($file, str_replace('&nbsp;',' ', $this->file_data));
        fclose($file);
        $this->file_data = '';
    } */

    private function writeData($data_type)
    {
        // Initialize the WP_Filesystem
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
    
        WP_Filesystem();
        global $wp_filesystem;
    
        // Get the file path
        $file_path = $this->core_files->getLocalPath($data_type);
    
        // Replace &nbsp; with space
        $file_data = str_replace('&nbsp;', ' ', $this->file_data);
    
        // Write to the file using WP_Filesystem
        if (!$wp_filesystem->put_contents($file_path, $file_data, FS_CHMOD_FILE)) {
            // Handle the error if file writing fails
            return false;
        }
    
        // Clear file data after writing
        $this->file_data = '';
    
        return true;
    }        

    public function transferDataFile()
    {
        $this->checkNonce('transfer_data_file');
        $this->checkCapabilities();

        $data_type = isset($_POST['data_type']) && !empty($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : '';

        // Get the local file path
        $file_path = realpath($this->core_files->getLocalPath($data_type));

        // Read file content
        $file_content = file_get_contents($file_path);

        // Prepare the body with multipart/form-data
        $boundary = wp_generate_password(24);
        $eol = "\r\n";

        $body = '';
        $body .= '--' . $boundary . $eol;
        $body .= 'Content-Disposition: form-data; name="purpose"' . $eol . $eol;
        $body .= 'assistants' . $eol;

        $body .= '--' . $boundary . $eol;
        $body .= 'Content-Disposition: form-data; name="file"; filename="' . basename($file_path) . '"' . $eol;
        $body .= 'Content-Type: ' . mime_content_type($file_path) . $eol . $eol;
        $body .= $file_content . $eol;
        $body .= '--' . $boundary . '--';

        $headers = array(
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
        );

        $response = wp_remote_post('https://api.openai.com/v1/files', array(
            'headers' => $headers,
            'body'    => $body,
            'timeout' => 60,
        ));

        if (is_wp_error($response)) {
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }

        $output = json_decode(wp_remote_retrieve_body($response));
        $this->checkError($output);
        $this->updateRemoteFileOption($data_type, $output);

        wp_die();
    }

    private function updateRemoteFileOption($data_type, $output)
    {
        $update = update_option($this->core_files->getWpOptionName($data_type), $output->id, false);

        if ($update) {
            $file_name = '<span class="text-bg-success px-2 py-1 rounded-1 small fw-bold">' . $output->id . '</span>';
            $this->response['success'] = true;
            // Translators: %s is replaced with the file ID from OpenAI server.
            $this->response['message'] = '<div class="text-success">' . sprintf(esc_html__('Remote file name updated to %s', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), wp_kses_post($file_name)) . '</div>';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div class="text-danger">' . esc_html__('Unable to update remote file name.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        }

        echo wp_json_encode($this->response);
    }

    public function __construct()
    {
        $this->setAll();
        // add_action('wp_ajax_checkFileStatus', array($this, 'checkFileStatus'));
        // add_action('wp_ajax_isBbFileWritable', array($this, 'isBbFileWritable'));
        // add_action('wp_ajax_addDataToFile', array($this, 'addDataToFile'));
        // add_action('wp_ajax_transferDataFile', array($this, 'transferDataFile'));
    }
}