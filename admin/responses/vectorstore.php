<?php

namespace BuddyBot\Admin\Responses;

class VectorStore extends \BuddyBot\Admin\Responses\MoRoot
{
    public function createVectorStore()
    {
        $this->checkNonce('create_vectorstore');
        $this->checkOpenaiKey(__('AI Training requires an OpenAI API key. Please configure your key in the BuddyBot settings to enable this feature.','buddybot-ai-custom-ai-assistant-and-chat-agent'));
        $this->checkCapabilities();

        $url = 'https://api.openai.com/v1/vector_stores';

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $vectorstore_data = json_decode(wp_unslash(sanitize_text_field($_POST['vectorstore_data'])), false);

        $data = array(
            'name' => $vectorstore_data->name,
        );

        $args = [
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'method' => 'POST'
        ];

        $this->openai_response = wp_remote_post($url, $args);
        $this->processResponse();

        if ($this->response['success'] == true) {
            $response_body = json_decode(wp_remote_retrieve_body($this->openai_response), true);

            if (isset($response_body['id']) && isset($response_body['name'])) {

                $vectorstore_data = [
                    'name' => $response_body['name'],
                    'id' => $response_body['id']
                ];
                update_option('buddybot_vectorstore_data', $vectorstore_data);
            }
        }
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function getVectorStore()
    {
        $this->checkNonce('get_vectorstore');

        $url = 'https://api.openai.com/v1/vector_stores?limit=10';

        $headers = [
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if ($this->openai_response_body->object === 'list') {
            $this->response = $this->matchAllVectorstore();
        } else {
            $this->response['success'] = false;
            $this->response['create_vectorstore'] = true;
            $this->response['message'] = esc_html__('Unable to fetch VectorStore list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function matchAllVectorstore()
    {
        if (!is_array($this->openai_response_body->data)) {
            return [
                'success' => false,
                'create_vectorstore' => true,
                'message' => esc_html__('Output is not an Array.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            ];
        }

        $hostname = wp_parse_url(home_url(), PHP_URL_HOST);

        if($hostname === 'localhost'){
            $path = wp_parse_url(home_url(), PHP_URL_PATH) ?? '';
            $hostname = $hostname . str_replace('/', '.', $path); 
        }

        $vectorstore_name = '';
        $vectorstore_id = '';
        foreach ($this->openai_response_body->data as $store) {
            if (isset($store->name, $store->id) && $store->name === $hostname) {
                $vectorstore_id = $store->id;
                $vectorstore_name = $store->name;
                break;
            }
        }

        if(!empty($vectorstore_id) && !empty($vectorstore_name)) {
            $vectorstore_data = [
                'name' => $vectorstore_name,
                'id' => $vectorstore_id
            ];
            update_option('buddybot_vectorstore_data', $vectorstore_data);
            return [
                'success' => true,
                'data'    => $vectorstore_data,
            ];
        } else {
            return [
                'success' => false,
                'create_vectorstore' => true,
                'message' => esc_html__('No matching VectorStore found.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            ];
        }
    }

    public function retrieveVectorStore()
    {
        $this->checkNonce('retrieve_vectorstore');

        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';

        $url = 'https://api.openai.com/v1/vector_stores/' . $vectorstore_id;

        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key,
            'OpenAI-Beta' => 'assistants=v2'
        );

        $response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 60,
        ));
       
        if (is_wp_error($response)) {
            if ($response->get_error_code() === 'http_request_failed') {
                $this->response['success'] = false;
                $this->response['message'] = esc_html__('Unable to verify file status due to a network timeout. Please try again later.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                echo wp_json_encode($this->response);
                wp_die();
            }
            $this->response['success'] = false;
            $this->response['message'] = $response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }

        $output = json_decode(wp_remote_retrieve_body($response), true);
        $this->checkError($output);

        if (isset($output['id']) && !empty($output['id'])) {
            $this->response['success'] = false;
            // Translators: %s represents the AI Training Knowledgebase ID.
            $this->response['message'] = sprintf(esc_html__('An AI Training Knowledgebase with ID %s already exists. Please use the existing AI Training Knowledgebase or choose a different ID.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($output['id']));
        } else {
            $this->response['success'] = true;
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function displayVectorStoreName()
    {

        $this->checkNonce('display_vectorstore_name');
        $this->checkCapabilities();

        $vectorstore_data = get_option('buddybot_vectorstore_data');

        if ($vectorstore_data && isset($vectorstore_data['name'])) {
            $this->response['success'] = true;
            /* translators: %s is the name of the vector store */
            $this->response['message'] = wp_kses_post(sprintf(__('<strong>New AI Training Knowledgebase Created:</strong> The name of the knowledgebase is: <span class="vectorstore-name">%s</span>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($vectorstore_data['name'])));
        } else {
            $this->response['success'] = false;
            $this->response['message'] = wp_kses_post(__('<strong>No AI Training Knowledgebase Found:</strong> BuddyBot requires a AI Training Knowledgebase to manage and retrieve contextual data. Click the button below to create one.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function deleteVectorStore()
    {
        $this->checkNonce('delete_vectorstore');
        $this->checkCapabilities();

        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';

        if (empty($vectorstore_id)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('VectorStore ID cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/vector_stores/' . $vectorstore_id;

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers, 'method' => 'DELETE'];

        $this->openai_response = wp_remote_post($url, $args);
        $this->processResponse();

        if ($this->response['result']->deleted) {
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Successfully deleted Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            // delete_option('buddybot_vectorstore_data');
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to delete the Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    protected $file_data = '';

    // public function checkFileStatus()
    // {
    //     $this->checkNonce('check_file_status');

    //     $file_id = isset($_POST['file_id']) && !empty($_POST['file_id']) ? sanitize_text_field($_POST['file_id']) : '';

    //     $url = 'https://api.openai.com/v1/files/' . sanitize_text_field($file_id);

    //     $headers = [
    //         'Authorization' => 'Bearer ' . $this->api_key
    //     ];

    //     $args = ['headers' => $headers];

    //     $this->openai_response = wp_remote_get($url, $args);
    //     $this->processResponse();

    //     $this->response['message'] = esc_html__('File syncronized!', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

    //     echo wp_json_encode($this->response);
    //     wp_die();
    // }

    public function isBbFileWritable()
    {
        $this->checkNonce('is_file_writable');
        $data_type = isset($_POST['data_type']) && !empty($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : '';
        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';
        $file = $this->core_files->getLocalPath($data_type);

        if (empty($vectorstore_id)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Upload failed. No AI Training Knowledgebase found. Please create one before uploading.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        WP_Filesystem();
        global $wp_filesystem;

        if ($wp_filesystem->is_writable($file)) {
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('The file is writable.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('The file is not writable.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
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
            $this->response['message'] = esc_html__('Data compile method undefined. Operation aborted.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $this->writeData($data_type);

        $this->response['success'] = true;
        $this->response['message'] = esc_html__('Added data to file.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function compilePosts()
    {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        $post_query = new \WP_Query($args);

        if ($post_query->have_posts()) {
            while ($post_query->have_posts()) {
                $post_query->the_post();
                $this->file_data .= wp_strip_all_tags(get_the_title()). "\r\n";
                $this->file_data .= wp_strip_all_tags(get_the_content()). "\r\n";
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
            $this->file_data .= wp_strip_all_tags($comment->comment_content). "\r\n";
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
        $hostname = wp_parse_url(home_url(), PHP_URL_HOST);
        if($hostname === 'localhost'){
            $path = wp_parse_url(home_url(), PHP_URL_PATH) ?? '';
            $hostname = $hostname . str_replace('/', '.', $path); 
        }
        $file_name = $hostname . '.' . basename($file_path);

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
        $body .= 'Content-Disposition: form-data; name="file"; filename="' . $file_name . '"' . $eol;
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
            if ($response->get_error_code() === 'http_request_failed') {
                $this->response['success'] = false;
                $this->response['message'] = esc_html__('Unable to verify file status due to a network timeout. Please try again later.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                echo wp_json_encode($this->response);
                wp_die();
            }
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

    private function filesListHtml($list)
    {
        $html = '';

        if (!empty($list)) {
            foreach ($list as $file) {
                // Build HTML for each file
                $html .= '<div class="file-item" data-file-id="' . esc_attr($file->id) . '">';
                $html .= '<h4>' . esc_html($file->filename) . '</h4>';
                $html .= '<p><strong>ID:</strong> ' . esc_html($file->id) . '</p>';
                $html .= '<p><strong>Status:</strong> ' . esc_html($file->status) . '</p>';
                // Add more details based on the API response data
                $html .= '</div>';
            }
        } else {
            $html .= '<p>No files available.</p>';
        }

        return $html;
    }

    private function updateRemoteFileOption($data_type, $output)
    {
        $update = update_option($this->core_files->getWpOptionName($data_type), $output->id, false);

        if ($update) {
            //$file_name = '<span class="text-bg-success px-2 py-1 rounded-1 small fw-bold">' . $output->id . '</span>';
            $this->response['success'] = true;
            $this->response['id'] = $output->id;
            // Translators: %s is replaced with the file ID from OpenAI server.
            $this->response['message'] = sprintf(esc_html__('Remote file name updated to %s', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), wp_kses_post($output->id));
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to update remote file name.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
    }

    private function fetchFilesByDataType($data_type)
    {
        $url = 'https://api.openai.com/v1/files';

        $headers = ['Authorization' => 'Bearer ' . $this->api_key];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        $file_ids = [];
        $domain = wp_parse_url(home_url(), PHP_URL_HOST);
        if($domain === 'localhost'){
            $path = wp_parse_url(home_url(), PHP_URL_PATH) ?? '';
            $domain = $domain . str_replace('/', '.', $path); 
        }

        if (isset($this->openai_response_body->data)) {
            foreach ($this->openai_response_body->data as $file) {

                // Check if the file name contains the data_type (e.g., 'comments' or 'posts')
                if (isset($file->filename) && strpos($file->filename, $domain) !== false && strpos($file->filename, $data_type) !== false) {
                    $file_ids[] = $file->id;
                }
            }
        }

        return $file_ids;
    }

    public function deleteOldFiles()
    {
        $this->checkNonce('delete_Old_Files');
        $this->checkCapabilities();

        $new_file_id = sanitize_text_field($_POST['file_Id']);
        $data_type = isset($_POST['data_type']) && !empty($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : '';
        $this->cleanLocalFile($data_type);

        $file_ids = $this->fetchFilesByDataType($data_type);

        foreach ($file_ids as $file_id) {

            if ($file_id != $new_file_id) {

                $url = 'https://api.openai.com/v1/files/' . $file_id;

                $headers = [
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                    'Authorization' => 'Bearer ' . $this->api_key
                ];

                $args = ['headers' => $headers, 'method' => 'DELETE'];

                $this->openai_response = wp_remote_post($url, $args);
                $this->processResponse();

                if (isset($this->response['result']) && $this->response['result']->deleted) {
                    $deleted_files[] = $file_id;
                } else {
                    $failed_files[] = $file_id;
                }
            }
        }
    
        if (!empty($deleted_files)) {
            $this->response['success'] = true;
            $deleted_files = implode(', ', $deleted_files);
            // Translators: %s is replaced with a list of successfully deleted files.
            $this->response['message'] = sprintf(esc_html__('Outdated file %s deleted from the AI Training Knowledgebase.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), wp_kses_post($deleted_files));
        }
        
        if (!empty($failed_files)) {
            $failed_files = implode(', ', $failed_files);
            // Translators: %s is replaced with the list of files that failed to delete.
            $this->response['message'] = sprintf(esc_html__('The file %s could not be deleted. Please check the file permissions or try again.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), wp_kses_post($failed_files));
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function cleanLocalFile($data_type)
    {
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        WP_Filesystem();
        global $wp_filesystem;

        $file_path = $this->core_files->getLocalPath($data_type);

        if (!$wp_filesystem->put_contents($file_path, '', FS_CHMOD_FILE)) {
            return false;
        }

        return true;
    }

    public function uploadFileIdsOnVectorStore()
    {
        $this->checkNonce('upload_File_Ids_On_Vector_store');
    
        $file_id = isset($_POST['file_id']) && !empty($_POST['file_id']) ? sanitize_text_field($_POST['file_id']) : '';
        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';
        $data_type = isset($_POST['data_type']) && !empty($_POST['data_type']) ? sanitize_text_field($_POST['data_type']) : '';

        if (empty($_POST['file_id']) || empty($_POST['vectorstore_id'])) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('File upload failed. No AI Training Knowledgebase found. Please create an AI Training Knowledgebase before initiating upload.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
        
        $url = 'https://api.openai.com/v1/vector_stores/' . $vectorstore_id . '/files';

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $data = array(
            'file_id' => $file_id
        );

        $args = [
            'headers' => $headers,
            'body' => wp_json_encode($data),
            'method' => 'POST'
        ];

        $this->openai_response = wp_remote_post($url, $args);
        $this->processResponse();
        $output = json_decode(wp_remote_retrieve_body($this->openai_response));
        $this->checkError($output);

        if (isset($output->id) && $output->id === $file_id) {
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('File uploaded to the AI Training Knowledgebase. Data will now be used by the AI Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->response['success'] = false;
           // $file_id = '<span class="text-bg-success px-2 py-1 rounded-1 small fw-bold">' . $output->id . '</span>';
            // Translators: %s is replaced with the list of files that failed to upload.
            $this->response['message'] = sprintf(esc_html__('File upload failed. The file ID %s does not exist in OpenAI. Please re-upload the file to continue.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), wp_kses_post($output->id));
        }

        echo wp_json_encode($this->response);
        wp_die();
    
    }

    public function getVectorStoreFiles()
    {
        $this->checkNonce('upload_File_Ids_On_Vector_store');

        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';

        $url = 'https://api.openai.com/v1/vector_stores/' . $vectorstore_id . '/files';

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        $output = json_decode(wp_remote_retrieve_body($this->openai_response));
        $this->checkError($output);

       // print_r($output);

        $this->response['html'] = $this->vectorStoreFilesHtml($this->openai_response_body->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function vectorStoreFilesHtml($list)
    {
        $html = '';

        if (!empty($list)) {
            foreach ($list as $file) {
                // Build HTML for each file
                $html .= '<div class="file-item" data-file-id="' . esc_attr($file->id) . '">';
                $html .= '<h4>' . esc_html($file->vector_store_id) . '</h4>';
                $html .= '<p><strong>ID:</strong> ' . esc_html($file->id) . '</p>';
                $html .= '</div>';
            }
        } else {
            $html .= '<p>No files available.</p>';
        }

        return $html;
    }

    public function checkFileStatusOnVectorStoreJs()
    {
        $this->checkNonce('check_file_status_On_Vector_Store');

        $file_id = isset($_POST['file_id']) && !empty($_POST['file_id']) ? sanitize_text_field($_POST['file_id']) : '';
        $vectorstore_id = isset($_POST['vectorstore_id']) && !empty($_POST['vectorstore_id']) ? sanitize_text_field($_POST['vectorstore_id']) : '';
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        if (empty($_POST['file_id']) || empty($_POST['vectorstore_id'])) {
            $this->response['success'] = false;
            $this->response['message'] = wp_kses_post('<strong>' . __('Status:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . __('Not Trained.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/vector_stores/' . $vectorstore_id . '/files/' . $file_id ;

        $headers = [
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers, 'timeout' => 10];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if (!empty($this->openai_response_body) && isset($this->openai_response_body->created_at) && is_numeric($this->openai_response_body->created_at)) {

            $gmt_time = gmdate('Y-m-d H:i:s', intval($this->openai_response_body->created_at));
            $last_sync = get_date_from_gmt($gmt_time,  $date_format . ' ' . $time_format);

            $this->response['success'] = true;
            $this->response['message'] = sprintf(wp_kses_post('<strong>Status:</strong> Successfully trained on %s.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($last_sync));
        } else {
            $this->response['success'] = false;
            $this->response['message'] = wp_kses_post('<strong>' . __('Status:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</strong> ' . __('Not Trained.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function deleteVectorStoreDatabase()
    {
        $this->checkNonce('delete_vectorstore_database');

        $option_deleted = delete_option('buddybot_vectorstore_data');

        if ($option_deleted) {
            $this->response['success'] = true;
            $this->response['message'] = sprintf(wp_kses_post('<strong>Unable to Access AI Training Knowledgebase:</strong> We couldn\'t access the AI Training Knowledgebase. This might happen if the AI Training Knowledgebase was deleted or the OpenAI API key was changed. Please verify the AI Training Knowledgebase exists and ensure the correct API key is configured in the <a href="%s">Settings</a>. Or, use the button below to create a new AI Training Knowledgebase.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_url(admin_url('admin.php?page=buddybot-settings')));
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Failed to delete AI Training Knowledgebase. Please check your network connection or try again later.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_createVectorStore', array($this, 'createVectorStore'));
        add_action('wp_ajax_getVectorStore', array($this, 'getVectorStore'));
        add_action('wp_ajax_retrieveVectorStore', array($this, 'retrieveVectorStore'));
        add_action('wp_ajax_deleteVectorStore', array($this, 'deleteVectorStore'));
        add_action('wp_ajax_checkFileStatusOnVectorStoreJs', array($this, 'checkFileStatusOnVectorStoreJs'));
        add_action('wp_ajax_isBbFileWritable', array($this, 'isBbFileWritable'));
        add_action('wp_ajax_addDataToFile', array($this, 'addDataToFile'));
        add_action('wp_ajax_transferDataFile', array($this, 'transferDataFile'));
        add_action('wp_ajax_getFiles', array($this, 'getFiles'));
        add_action('wp_ajax_deleteOldFiles', array($this, 'deleteOldFiles'));
        add_action('wp_ajax_displayVectorStoreName', array($this, 'displayVectorStoreName'));
        add_action('wp_ajax_uploadFileIdsOnVectorStore', array($this, 'uploadFileIdsOnVectorStore'));
        add_action('wp_ajax_getVectorStoreFiles', array($this, 'getVectorStoreFiles'));
        add_action('wp_ajax_deleteVectorStoreDatabase', array($this, 'deleteVectorStoreDatabase'));
    }
}
