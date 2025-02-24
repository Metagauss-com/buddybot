<?php

namespace BuddyBot\Admin\Responses;

class BuddyBots extends \BuddyBot\Admin\Responses\MoRoot
{

    public function deleteBuddyBot()
    {
        $this->checkNonce('delete_buddybot');
        $this->checkCapabilities();

        $assistant_id = isset($_POST['assistant_id']) && !empty($_POST['assistant_id']) ? sanitize_text_field($_POST['assistant_id']) : '';
        $chatbot_id = isset($_POST['chatbot_id']) && !empty($_POST['chatbot_id']) ? intval($_POST['chatbot_id']) : 0;

        if (empty($assistant_id) || $chatbot_id <= 0) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Assistant ID/ChatBot ID cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
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
            
            $response = $this->sql->deleteChatbot($chatbot_id);
            if ($response === false) {
                global $wpdb;
                $this->response['success'] = false;
                $this->response['message'] = $wpdb->last_error;
                echo wp_json_encode($this->response);
                wp_die();
            } 
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Successfully deleted BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to delete the BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();

    }

    private function buddybotTableHtml($response)
    {

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        $html = '';

        foreach ($response as $buddybot) {
            $html .= '<tr class="buddybot-chatbot-table-row">';
        
        $html .= '<td class="column-name">' . esc_html($buddybot['chatbot_name']) . '</td>';
        $html .= '<td class="column-description">' . esc_html($buddybot['assistant_id']) . '</td>';
        $html .= '<td class="column-shortcode">' . esc_html('[buddybot_chat id=' . esc_attr($buddybot['id']) . ']') . '</td>';
        $html .= '<td class="column-created">' . esc_html(get_date_from_gmt($buddybot['created_on'], $date_format . ' ' . $time_format)) . '</td>';
        $html .= '<td class="column-updated">' . esc_html(get_date_from_gmt($buddybot['edited_on'], $date_format . ' ' . $time_format)) . '</td>';
        $html .= '<td class="column-buttons">' . $this->buddybotBtns($buddybot['id']) . '</td>';
        
        // Close the row
        $html .= '</tr>';
        }

        return $html;
    }

    protected function buddybotBtns($buddybot_id)
    {   
        $assistant_url = get_admin_url() . 'admin.php?page=buddybot-chatbot&chatbot_id=' . $buddybot_id;
        $html = '<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic example">';
        $html .= '<a href="' . esc_url($assistant_url) . '" type="button" class="buddybot-listbtn-assistant-edit btn btn-outline-dark">' . $this->moIcon('edit') . '</a>';
        $html .= '<button type="button" class="buddybot-chatbot-delete btn btn-outline-dark" data-buddybot-itemid="' . esc_html($buddybot_id) . '">' . $this->moIcon('delete') . '</button>';
        $html .= '</div>';

        $html .= $this->listSpinner();
        
        return $html;
    }

    public function savePaginationLimit()
    {
        $this->checkNonce('pagination_dropdown');

        $limit = isset($_POST['selected_value']) ? absint($_POST['selected_value']) : 10;

        if (!update_option('buddybot_columns_per_page', $limit)) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Failed to update the setting.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
        //update_option('buddybot_columns_per_page', $limit);
        $this->response['success'] = true;

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function getModels()
    {
        $this->checkNonce('get_models');

        $url = 'https://api.openai.com/v1/models';

        $headers = [
            'Authorization' => 'Bearer ' . $this->api_key
        ];

        $args = ['headers' => $headers];

        $this->openai_response = wp_remote_get($url, $args);
        $this->processResponse();

        if ($this->openai_response_body->object === 'list') {
            $this->response['success'] = true;
            $this->response['list'] = $this->openai_response_body->data;
            $this->response['html'] = $this->modelsListHtml($this->openai_response_body->data);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Unable to fetch models list.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function modelsListHtml($list)
    {
        $unsupported_models = $this->config->getProp('unsupported_models');

        $html = '';

        if (!is_array($list) or empty($list)) {
            return $html;
        }

        foreach ($list as $model) {

            if (!in_array($model->id, $unsupported_models)) {
                $html .= '<option value="' . esc_attr($model->id) . '">';
                $html .= esc_html(strtoupper(str_replace('-', ' ', $model->id)));
                $html .= '</option>';
            }
        }

        return $html;
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_deleteBuddyBot', array($this, 'deleteBuddyBot'));
        add_action('wp_ajax_savePaginationLimit', array($this, 'savePaginationLimit'));
        add_action('wp_ajax_getModels', array($this, 'getModels'));
    }
}