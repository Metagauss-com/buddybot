<?php

namespace BuddyBot\Admin\Html\Views;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use WP_List_Table;

use BuddyBot\Admin\Sql\BuddyBots;

class BbTable extends WP_List_Table 
{
    private $bot_db;

    function __construct() {
        parent::__construct([
            'singular' => 'buddybot',
            'plural'   => 'buddybots',
            'ajax'     => false
        ]);

        $this->bot_db = new BuddyBots($data="");
    }

    function get_columns() {
        return [
            'chatbot_name' => __('BuddyBot Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'assistant_id' => __('Assistant Model', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'id'           => __('Shortcode', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'created_on'   => __('Date Created', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'edited_on'    => __('Last Updated', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
        ];
    }

    function get_sortable_columns() {
        return [
            'chatbot_name' => ['chatbot_name', false],
            'created_on'   => ['created_on', false],
            'edited_on'    => ['edited_on', false],
        ];
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = get_option('buddybot_columns_per_page', 10);
        $search = $_GET['s'] ?? '';
        $filter = $_GET['buddybot-filter-model'] ?? '';

        $current_page = $this->get_pagenum();
        $total_items  = $this->bot_db->getTotalChatbotsCount($search, $filter);

        $orderby = $_GET['orderby'] ?? 'created_on';
        $order   = $_GET['order'] ?? 'desc';

        $this->items = $this->bot_db->getAllChatbots(
            ($current_page - 1) * $per_page,
            $per_page,
            $orderby,
            $order,
            $search,
            $filter
        );

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

    public function column_default($item, $column_name)
    {
        if ($column_name === 'id') {
            return '[buddybot id="' . esc_attr($item[$column_name]) . '"]';
        }
        if (in_array($column_name, ['created_on', 'edited_on'])) {
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
    
            return esc_html(get_date_from_gmt($item[$column_name], $date_format . ' ' . $time_format));
        }

        return isset($item[$column_name]) ? esc_html($item[$column_name]) : '';
    }

    function column_chatbot_name($item) {
        $edit_link = get_admin_url() . 'admin.php?page=buddybot-chatbot&chatbot_id=' . intval($item['id']);
        //$delete_link = '';
    
        $chatbot_name_link = sprintf(
            '<strong><a href="%s" class="row-title">%s</a></strong>',
            esc_url($edit_link),
            esc_html($item['chatbot_name'])
        );
    
        $actions = [
            'edit'   => sprintf('<a href="%s">%s</a>', esc_url($edit_link), __('Edit', 'buddybot-ai-custom-ai-assistant-and-chat-agent')),
            'delete' => sprintf(
                '<a href="javascript:void(0)" class="buddybot-column-delete" data-id="%d">%s</a>',
                intval($item['id']),
                __('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
            ),
        ];
    
        return sprintf('%1$s %2$s', $chatbot_name_link, $this->row_actions($actions));
    }
    
    function extra_tablenav($which)
    {
        if ($which === 'top') {
            ?>
            <label for="buddybot-filter-model" class="screen-reader-text"><?php _e('Filter by Model', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></label>
            <select name="buddybot-filter-model" id="buddybot-filter-model">
                <option value=""><?php _e('All Models', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></option>
                <option value="" disabled><?php _e('Loading models...', 'buddybot-ai-custom-ai-assistant-and-chat-agent'); ?></option>
            </select>
            <?php
            submit_button(__('Filter'), '', 'filter_action', false);
        }
    }

    public function get_views() 
    {
        $total_count = $this->bot_db->getTotalChatbotsCount();
        $views = [];

        $current_filter = isset($_GET['buddybot-filter-model']) ? sanitize_text_field($_GET['buddybot-filter-model']) : '';
        $current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : ''; 
        $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';

        // If no filters or sorting are applied, mark "All" as current
        $is_all_current = empty($current_filter) && empty($current_search) && empty($current_orderby) ? 'current' : '';

        // "All" should remove all query parameters and reset the view
        $views['all'] = sprintf(
            '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
            esc_url(remove_query_arg(['buddybot-filter-model', 'paged', 'orderby', 'order', 's'])), 
            $is_all_current,
            __('All', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            $total_count
        );

        return $views;
    }
    
}
