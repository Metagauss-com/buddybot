<?php

namespace MetagaussOpenAI\Admin\Responses;

class DataSync extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    protected $file_data = '';
    protected $data_files = array();

    protected function setDataFiles()
    {
        $this->data_files['posts'] = $this->config->getRootPath() . 'data/posts.txt';
        $this->data_files['comments'] = $this->config->getRootPath() . 'data/comments.txt';
    }

    public function checkFileStatus()
    {
        $this->checkNonce('check_file_status');
        $file_id = $_POST['file_id'];

        $url = 'https://api.openai.com/v1/files/' . $file_id;
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = curl_exec($ch);
        curl_close($ch);

        $output = json_decode($output);

        if (!property_exists($output, 'error')) {
            $this->response['success'] = true;
            $this->response['message'] = __('File syncronized!', 'metagauss-openai');
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Remote file not found.', 'metagauss-openai');
        }

        echo json_encode($this->response);
        wp_die();
    }

    public function isFileWritable()
    {
        $this->checkNonce('is_file_writable');
        $data_type = $_POST['data_type'];
        $file = $this->data_files[$data_type];

        if (is_writable($file)) {
            $this->response['success'] = true;
            $this->response['message'] = '<div>' . __('The file is writable.', 'metagauss-openai') . '</div>';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('The file is not writable.', 'metagauss-openai');
        }

        echo json_encode($this->response);
        wp_die();
    }

    public function addDataToFile()
    {
        $this->checkNonce('add_data_to_file');
        
        $data_type = $_POST['data_type'];

        $method = 'compile' . $data_type;

        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->response['success'] = true;
            $this->response['message'] = '<div>' . __('Data compile method undefined.', 'metagauss-openai') . '</div>';
            wp_die();
        }

        $this->writeData($data_type);
        
        $this->response['success'] = true;
        $this->response['message'] = '<div>' . __('Added data to file.', 'metagauss-openai') . '</div>';

        echo json_encode($this->response);
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
                $this->file_data .= strip_tags(get_the_title());
                $this->file_data .= strip_tags(get_the_content());
            }
        }
        wp_reset_postdata();
    }

    private function writeData($data_type)
    {
        $data_file = fopen($this->data_files[$data_type], "w");
        fwrite($data_file, str_replace('&nbsp;',' ', $this->file_data));
        fclose($data_file);
        $this->file_data = '';
    }

    public function transferDataFile()
    {
        $this->checkNonce('transfer_data_file');
        $data_type = $_POST['data_type'];

        $cfile = curl_file_create(
            $this->data_files[$data_type],
            'text/plain',
            $data_type
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
        curl_close($ch);

        if ($output != false) {
            $output = json_decode($output);
            $this->updateRemoteFileOption($data_type, $output);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Unable to transfer file.', 'metagauss-openai') . '</div>';
            echo json_encode($response);
        }

        wp_die();
    }

    private function updateRemoteFileOption($data_type, $output)
    {
        $update = update_option('mo-' . $data_type . '-remote-file-id', $output->id, false);

        if ($update) {
            $this->response['success'] = true;
            $this->response['message'] = '<div>' . __(sprintf('Remote file name updated to %s.', $output->id), 'metagauss-openai') . '</div>';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Unable to update remote file name.', 'metagauss-openai') . '</div>';
        }

        echo json_encode($this->response);
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_checkFileStatus', array($this, 'checkFileStatus'));
        add_action('wp_ajax_isFileWritable', array($this, 'isFileWritable'));
        add_action('wp_ajax_addDataToFile', array($this, 'addDataToFile'));
        add_action('wp_ajax_transferDataFile', array($this, 'transferDataFile'));
    }
}