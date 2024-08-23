<?php

namespace BuddyBot\Admin\Responses;

class Assistants extends \BuddyBot\Admin\Responses\MoRoot
{
    
    public function deleteAssistant()
    {
        $this->checkNonce('delete_assistant');
        $this->checkCapabilities();

        $assistant_id = sanitize_text_field($_POST['assistant_id']);

        if (empty($assistant_id)) {
            $this->response['success'] = false;
            $this->response['message'] = __('Assistant ID cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $url = 'https://api.openai.com/v1/assistants/' . $assistant_id;

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
            $this->response['message'] = __('Successfully deleted Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to delete the Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function getAssistants()
    {
        $this->checkNonce('get_assistants');

        $after = '';

        if (!empty(sanitize_text_field($_POST['after']))) {
            $after = '&after=' . sanitize_text_field($_POST['after']);
        }

        $url = 'https://api.openai.com/v1/assistants?limit=10' . sanitize_text_field($after);
        
        $headers = [
            'OpenAI-Beta' => 'assistants=v1',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if ($this->openai_response_body->object === 'list') {
            $this->response['success'] = true;
            $this->assistantsTableHtml(absint($_POST['current_count']));
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to fetch assistants list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function assistantsTableHtml($current_count)
    {
        if (!is_array($this->openai_response_body->data)) {
            return;
        }

        $html = '';

        foreach ($this->openai_response_body->data as $index => $assistant) {
            $index = absint($current_count) + $index + 1;
            $html .= '<tr class="small buddybot-assistant-table-row" data-buddybot-itemid="' . esc_attr($assistant->id) . '">';
            $html .= '<th class="buddybot-assistants-sr-no" scope="row">' . absint($index) . '</th>';
            $html .= '<td>' . esc_html($assistant->name) . '</td>';
            $html .= '<td>' . esc_html($assistant->description) . '</td>';
            $html .= '<td>' . esc_html($assistant->model) . '</td>';
            $html .= '<td><code>' . esc_html($assistant->id) . '</code></td>';
            $html .= '<td>' . $this->assistantBtns($assistant->id) . '</td>';
            $html .= '</tr>';
        }

        $this->response['html'] = $html;
    }

    protected function assistantBtns($assistant_id)
    {   
        $assistant_url = get_admin_url() . 'admin.php?page=buddybot-assistant&assistant_id=' . $assistant_id;
        $html = '<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic example">';
        $html .= '<a href="' . esc_url($assistant_url) . '" type="button" class="buddybot-listbtn-assistant-edit btn btn-outline-dark">' . $this->moIcon('edit') . '</a>';
        $html .= '<button type="button" class="buddybot-listbtn-assistant-delete btn btn-outline-dark" data-buddybot-itemid="' . esc_html($assistant_id) . '">' . $this->moIcon('delete') . '</button>';
        $html .= '</div>';

        $html .= $this->listSpinner();
        
        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_deleteAssistant', array($this, 'deleteAssistant'));
        add_action('wp_ajax_getAssistants', array($this, 'getAssistants'));
    }
}