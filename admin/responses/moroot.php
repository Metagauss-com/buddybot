<?php

namespace BuddyBot\Admin\Responses;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    public $config;
    protected $options;
    protected $openai_response = '';
    protected $openai_response_body = '';
    protected $response = array();
    protected $api_key;
    protected $core_files;
    protected $sql;

    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $file_path = $this->config->getRootPath() . 'admin/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\Admin\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance();
        }
    }

    protected function setApiKey()
    {
        $this->api_key = $this->options->getOption('openai_api_key');
    }

    protected function setCoreFiles()
    {
        $this->core_files = \BuddyBot\Admin\CoreFiles::getInstance();
    }

    protected function checkNonce($nonce)
    {
        $nonce_status = wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['nonce'])),
            $nonce
        );

        if ($nonce_status === false) {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . esc_html__('Nonce error.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
            $this->response['errors'] = array(esc_html__('Nonce check failed.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
            echo wp_json_encode($this->response);
            wp_die();
        }
    }

    protected function checkOpenaiKey($message)
    {
        if(empty($this->api_key)){
            $this->response['success'] = false;
            $this->response['message'] = esc_html__($message, 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            $this->response['empty_key'] = true;
            echo wp_json_encode($this->response);
            wp_die();
        }
    }

    protected function checkCapabilities()
    {
        if (!(current_user_can('manage_options'))) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('You do not have permission to do this.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
    }

    protected function checkError($output)
    {
        if (is_scalar($output)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Invalid data structure. ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . ' ' . maybe_serialize($output);
            echo wp_json_encode($this->response);
            wp_die();
        } elseif (!empty($output->error)) {
            $this->response['success'] = false;
            $this->response['message'] = '<span class="text-danger">' . esc_html__('There was an error. ', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            $this->response['message'] .= $output->error->message . '</span>';
            echo wp_json_encode($this->response);
            wp_die();
        } else {
            $this->response['success'] = true;
            $this->response['result'] = $output;
        }
    }

    protected function processResponse()
    {
        if (is_wp_error($this->openai_response)) {

                if ($this->openai_response->get_error_code() === 'http_request_failed') {
                    $this->response['success'] = false;
                    $this->response['message'] = esc_html__('Unable to verify file status due to a network timeout. Please try again later.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo wp_json_encode($this->response);
                    wp_die();
                }

            $this->response['success'] = false;
            $this->response['message'] = $this->openai_response->get_error_message();
            echo wp_json_encode($this->response);
            wp_die();
        }

        $openai_response_body = wp_remote_retrieve_body($this->openai_response);
        $this->openai_response_body = json_decode($openai_response_body);

        if (!is_object($this->openai_response_body)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Output is not an object. ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . ' ' . maybe_serialize($this->openai_response_body);
            echo wp_json_encode($this->response);
            wp_die();
        } elseif (isset($this->openai_response_body->error) && !empty($this->openai_response_body->error)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('There was an error. ', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            if (isset($this->openai_response_body->error->message)){
                $this->response['message'] .= esc_html(str_replace('vector store', esc_html__('AI Training Knowledgebase', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), $this->openai_response_body->error->message));
            }
            echo wp_json_encode($this->response);
            wp_die();
        }

        $this->response['success'] = true;
        $this->response['result'] = $this->openai_response_body;
    }

    protected function moIcon($icon)
    {
        $html = '<span class="material-symbols-outlined" style="font-size:20px;vertical-align:sub;">';
        $html .= esc_html($icon);
        $html .= '</span>';
        return $html;
    }

    protected function listBtns($item_type)
    {
        $info_btn_class = 'buddybot-listbtn-' . $item_type . '-info';
        $delete_btn_class = 'buddybot-listbtn-' . $item_type . '-delete';

        $html = '<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic example">';
        $html .= '<button type="button" class="' . esc_attr($info_btn_class) . ' btn btn-outline-dark">' . $this->moIcon('info') . '</button>';
        $html .= '<button type="button" class="' . esc_attr($delete_btn_class) . ' btn btn-outline-dark">' . $this->moIcon('delete') . '</button>';
        $html .= '</div>';

        $html .= $this->listSpinner();

        return $html;
    }

    protected function listSpinner()
    {
        $html = '<div class="buddybot-list-spinner spinner-border spinner-border-sm visually-hidden" role="status">';
        $html .= '<span class="visually-hidden">Loading...</span></div>';
        return $html;
    }
}