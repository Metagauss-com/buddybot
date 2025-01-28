<?php

namespace BuddyBot\Admin\Responses;

class Conversations extends \BuddyBot\Admin\Responses\MoRoot
{

    public function getConversations()
    {
        $this->checkNonce('get_conversations');

        $paged = isset($_POST['paged']) ? absint($_POST['paged']) : 1;
        $limit = get_option('buddybot_conversations_per_page', 10);
        //$limit = 1;
        $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;

        $offset =(int) ( $paged-1 ) * $limit;
        $total_count = $this->sql->getTotalConversationsCount($user_id);
        $response = $this->sql->getAllConversations($offset, $limit, $user_id);

        if ($response === false) {
            global $wpdb;
            $response = array(
                'success' => false,
                'message' => $wpdb->last_error
            );
            echo wp_json_encode($response);
            wp_die();
        }
        $paged++;
        $conversations = $this->buddybotConversationsTableHtml($response, $offset, $paged);

        $response = array(
            'success' => true,
            'html' => $conversations,
        );

        if ($total_count > ($offset + $limit)) {
            $response['has_more'] = $total_count > ($offset + $limit);
        }

        echo wp_json_encode($response);
        wp_die();
    }

    private function buddybotConversationsTableHtml($response, $offset, $paged)
    {

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        $html = '';

        foreach ($response as $index => $conversation) {
            $index = absint($offset) + $index + 1;
            $html .= '<tr class="small buddybot-conversations-table-row buddybot-col-no" data-buddybot-itemid="' . esc_attr($conversation['thread_id']) . '" data-buddybot-pageid="' . esc_attr($paged) . '">';
            $html .= '<th class="buddybot-conversations-sr-no" scope="row">' . absint($index)  . '</th>';
            $html .= '<td class="text-truncate buddybot-conversations-name">' . esc_html($conversation['thread_name']) . '</td>';
            $user_info = get_userdata($conversation['user_id']);
            if ($user_info) {
            $User_name = $user_info->display_name;
            } else {
            $User_name = __('Unknown User', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            }
            $html .= '<td class="text-truncate buddybot-conversations-user">' . esc_html($User_name) . '</td>';
            //$html .= '<td class="buddybot-conversations-shortcode"><code>' . esc_html('[buddybot_chat id=' . esc_attr($budybot['id']) . ']') . '</code></td>';
            $html .= '<td class="buddybot-conversations-creation">' . esc_html(get_date_from_gmt($conversation['created'],  $date_format . ' ' . $time_format)) . '</td>';
            $html .= '<td class="buddybot-conversations-btn">' . $this->conversationBtns($conversation['thread_id'], $conversation['user_id']) . '</td>';
            $html .= '</tr>';
        }

        return $html;
    }

    protected function conversationBtns($thread_id, $user_id)
    {   
        $conversation_url = get_admin_url() . 'admin.php?page=buddybot-viewconversation&thread_id=' . $thread_id . '&user_id=' . $user_id;
        $html = '<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic example">';
        $html .= '<a href="' . esc_url($conversation_url) . '" type="button" class="buddybot-listbtn-conversation-view btn btn-outline-dark">' . $this->moIcon('visibility') . '</a>';
        $html .= '<button type="button" class="buddybot-conversation-delete btn btn-outline-dark" data-buddybot-itemid="' . esc_html($thread_id) . '">' . $this->moIcon('delete') . '</button>';
        $html .= '</div>';

        $html .= $this->listSpinner();
        
        return $html;
    }

    public function saveConversationLimitPerPage()
    {
        $this->checkNonce('save_conversation_limit_per_page');

        $limit = isset($_POST['selected_value']) ? absint($_POST['selected_value']) : 10;
        if(!empty($limit)){
            update_option('buddybot_conversations_per_page', $limit);
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Successfully saved conversation limit per page.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }else{
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Failed to save conversation limit per page.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getConversations', array($this, 'getConversations'));
        add_action('wp_ajax_saveConversationLimitPerPage', array($this, 'saveConversationLimitPerPage'));
        
    }

}