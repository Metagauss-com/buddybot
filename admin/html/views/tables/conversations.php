<?php

namespace BuddyBot\Admin\Html\Views\Tables;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;

//use Conversations;

class Conversations extends WP_List_Table
{
    private $bot_db;

    function __construct() {
        parent::__construct([
            'singular' => 'conversation',
            'plural'   => 'conversations',
            'ajax'     => false
        ]);

        $this->bot_db = new \BuddyBot\Admin\Sql\Conversations($data="");
    }

    function get_columns() {
        return [
            'thread_name' => esc_html__('Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'thread_id'   => esc_html__('ID', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'user_name'   => esc_html__('User', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'created'     => esc_html__('Date', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
        ];
    }

    function get_sortable_columns() {
        return [
            'thread_name' => ['thread_name', false],
            'created'   => ['created', false],
        ];
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = intval(get_option('buddybot_conversations_per_page', 10));
        $search   = sanitize_text_field($_GET['s'] ?? '');
        $user_id   = sanitize_text_field($_GET['buddybot-filter-user'] ?? '');
        if (empty($user_id) && isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $user_id = sanitize_text_field($_GET['user_id']);
        }

        $user_id   = sanitize_text_field($_GET['buddybot-filter-user'] ?? '');
        $user_type   = sanitize_text_field($_GET['buddybot-filter-user-type'] ?? '');
        $current_page = $this->get_pagenum();
        $total_items  = $this->bot_db->getTotalConversationsCount($user_id, $search);

        $orderby = $_GET['orderby'] ?? 'created_on';
        $order   = $_GET['order'] ?? 'desc';

        $this->items = $this->bot_db->getAllConversations(
            ($current_page - 1) * $per_page,
            $per_page,
            $orderby,
            $order,
            $user_id,
            $user_type,
            $search
        );

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

    public function column_default($item, $column_name)
    {
        if ($column_name === 'created') {
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
    
            return esc_html(get_date_from_gmt($item[$column_name], $date_format . ' ' . $time_format));
        }
    
        return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
    }

    function column_thread_name($item) {
        $view_link = get_admin_url() . 'admin.php?page=buddybot-viewconversation&thread_id=' . sanitize_text_field($item['thread_id']) . '&user_id=' . sanitize_text_field($item['user_id']);

    
        $thread_name_link = sprintf(
            '<strong><a href="%s" class="row-title">%s</a></strong>',
            esc_url($view_link),
            esc_html($item['thread_name'])
        );
    
        $actions = [
            'edit'   => sprintf('<a href="%s">%s</a>', esc_url($view_link), __('View', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),
            'delete' => sprintf(
                '<a href="javascript:void(0)" class="buddybot-conversation-delete" data-modal="buddybot-del-conversation-modal" thread-id="%s">%s</a>',
                esc_attr($item['thread_id']),
                __('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
            ),
        ];
    
        return sprintf('%1$s %2$s', $thread_name_link, $this->row_actions($actions));
    }

    function column_user_name($item) {
        $user_id = isset($item['user_id']) ? intval($item['user_id']) : 0;
        $user_info = get_userdata($user_id);
        return $user_info ? esc_html($user_info->display_name) : esc_html__('Visitor', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }
    
    function extra_tablenav($which)
{
    if ($which === 'top') {
        $user_ids = $this->bot_db->getUserIds();
        $selected_user_type = isset($_GET['buddybot-filter-user-type']) ? $_GET['buddybot-filter-user-type'] : '';
        ?>
        <label for="buddybot-filter-user" class="screen-reader-text"><?php esc_html_e('Filter by User', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></label>
        <select name="buddybot-filter-user" id="buddybot-filter-user">
            <option value=""><?php _e('All Users', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></option>
            <?php
            if (!empty($user_ids)) {
                $users = get_users([
                    'include' => $user_ids,
                    'fields'  => ['ID', 'display_name']
                ]);

                foreach ($users as $user) {
                    $selected = isset($_GET['buddybot-filter-user']) && $_GET['buddybot-filter-user'] == $user->ID ? 'selected' : '';
                    printf(
                        '<option value="%d" %s>%s</option>',
                        esc_attr($user->ID),
                        $selected,
                        esc_html($user->display_name)
                    );
                }
            } else {
                echo '<option disabled>' . esc_html__('No Users Found', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
            }
            ?>
        </select>

        <label for="buddybot-filter-user-type" class="screen-reader-text"><?php esc_html_e('Filter by User Type', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></label>
        <select name="buddybot-filter-user-type" id="buddybot-filter-user-type">
            <option value=""><?php _e('All User Types', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></option>
            <option value="logged_in" <?php selected($selected_user_type, 'logged_in'); ?>><?php esc_html_e('Logged In', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></option>
            <option value="visitor" <?php selected($selected_user_type, 'visitor'); ?>><?php esc_html_e('Visitor', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></option>
        </select>
        <?php
        submit_button(__('Filter'), '', 'filter_action', false);
    }
}

    public function get_views() 
    {
        $total_count = $this->bot_db->getTotalConversationsCount();
        $views = [];

        $current_filter = isset($_GET['buddybot-filter-user']) ? sanitize_text_field($_GET['buddybot-filter-user']) : '';
        $current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : ''; 
        $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';

        // If no filters or sorting are applied, mark "All" as current
        $is_all_current = empty($current_filter) && empty($current_search) && empty($current_orderby) ? 'current' : '';

        // "All" should remove all query parameters and reset the view
        $views['all'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
            esc_url(remove_query_arg(['buddybot-filter-user', 'paged', 'orderby', 'order', 's'])), 
            $is_all_current,
            __('All', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            $total_count
        );

        return $views;
    }
    
}
