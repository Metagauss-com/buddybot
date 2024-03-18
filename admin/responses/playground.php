<?php

namespace MetagaussOpenAI\Admin\Responses;

class Playground extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function getAssistantOptions()
    {
        $this->checkNonce('get_assistants');

        $url = 'https://api.openai.com/v1/assistants?limit=50';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        if ($output->object === 'list') {
            $this->response['success'] = true;
            $this->assistantOptions($output);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to fetch assistants list.', 'metagauss-openai');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    protected function assistantOptions($assistants)
    {
        $this->response['html'] = '';

        if (!is_array($assistants->data)) {
            return;
        }

        foreach ($assistants->data as $assistant) {
            $name = $assistant->name;
            $id = $assistant->id;

            if (empty($name)) {
                $name = $assistant->id;
            }

            $this->response['html'] .= '<option value="' . $id . '">' . $name . '</option>';
        }
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getAssistantOptions', array($this, 'getAssistantOptions'));
    }
}