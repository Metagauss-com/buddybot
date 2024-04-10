<?php

namespace MetagaussOpenAI\Admin\Responses;

class ChatBot extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function selectAssistantModal()
    {
        $this->checkNonce('select_assistant_modal');

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

        $this->response['html'] = $this->getAssistantListHtml($output->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function getAssistantListHtml($list)
    {
        $assisstant_list = new \MetagaussOpenAI\Admin\Html\Elements\Chatbot\AssistantList();

        $html = '';

        foreach ($list as $assistant) {
            $assisstant_list->listItem($assistant);
            $html .= $assisstant_list->getHtml();
        }

        return $html;
    }
   
    public function __construct()
    {
        add_action('wp_ajax_selectAssistantModal', array($this, 'selectAssistantModal'));
    }
}