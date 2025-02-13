<?php

namespace BuddyBot\Admin\Html\Views;

final class BuddyBots extends \BuddyBot\Admin\Html\Views\MoRoot
{

    public function getHtml()
    {
        $this->pageModals();
        $this->alertContainer();
        $heading = __('BuddyBots', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->customPageHeading($heading);
        $this->assistantsTable();
    }

    protected function pageModals()
    {
        $deleteBuddybot = new \BuddyBot\Admin\Html\Modals\DeleteBuddyBot();
        $deleteBuddybot->getHtml();
    }

    protected function customPageHeading($heading)
    {
        $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        echo '<div class="buddybot-header-wrap">';
        echo '<div class="buddybots-page-heading">';
        echo '<h1 class="wp-heading-inline">';
        echo esc_html($heading);
        echo '</h1>';
        $this->pageBtns();
        if (!empty($search_query)) {
            printf(
                '<span class="subtitle">' . esc_html__('Search results for: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '<strong>%s</strong></span>',
                esc_html($search_query)
            );
        }
        echo '</div>';
        $this->paginationDropdown();
        echo '</div>';
    }

    public function searchBar()
    {
        echo '<form method="get" class="search-form">';
        echo '<input type="text" name="s" placeholder="' . esc_attr(__('Search BuddyBots...', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '" value="' . esc_attr($_GET['s'] ?? '') . '" />';
        echo '<button type="submit" class="button">' . esc_html(__('Search', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</button>';
        echo '</form>';
    }

    public function pageBtns()
    {
        $add_assistant_page = get_admin_url() . 'admin.php?page=buddybot-editassistant';
        //echo '<div class="">';
        echo '<a href="' . esc_url($add_assistant_page) . '" class="page-title-action">';
        echo esc_html(__('Create New BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</a>';
        //echo '</div>';
    }

    private function paginationDropdown()
    {
        
        $saved_value = esc_attr(get_option('buddybot_columns_per_page', 10));

        echo '<div id="buddybot-chatbot-dropdown">';
        echo '<label>' . esc_html__('Results per page →', 'multiple-buddybots') . '</label>';
        echo '<select id="buddybot-chatbot-pagination">';
        
        $options = [10, 20, 30, 40, 50];
        
        foreach ($options as $option) {
            $option_value = esc_attr($option);
            $selected = ($saved_value == $option_value) ? 'selected' : '';
            echo '<option value="' . $option_value . '" ' . esc_attr($selected) . '>' . esc_html__('page 1-' . $option_value, 'multiple-buddybots') . '</option>';
        }
        
        echo '</select>';
        echo '</div>';
    }

    private function assistantsTable()
    {
        // echo '<table class="wp-list-table widefat fixed striped posts buddybot-org-buddybots-table">';
        // $this->tableHeader();
        // $this->tableBody();
        // $this->tableFooter();
        // echo '</table>';
        // $this->pagination();        
        if (class_exists('\BuddyBot\Admin\Html\Views\BbTable')) {
            $buddybots_table = new \BuddyBot\Admin\Html\Views\BbTable();

            $buddybots_table->prepare_items();

            $buddybots_table->views();
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="' . esc_attr($_GET['page'] ?? '') . '">';

            $buddybots_table->search_box(__('Search', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), 's');
            $buddybots_table->display();

            echo '</form>';
        } else {
            echo '<p>Error: Class BbTable not found!</p>';
        }

    }

    private function tableHeader()
    {
        echo '<thead><tr>';
        $this->sortableColumn('chatbot_name', __('BuddyBot Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '<th scope="col" class="manage-column column-modal">' . esc_html(__('Assistant Model', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th scope="col" class="manage-column column-assistant">' . esc_html(__('Assistant Id', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th scope="col" class="manage-column column-shortcode">' . esc_html(__('Shortcode', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        $this->sortableColumn('created_on', __('Date Created', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        $this->sortableColumn('edited_on', __('Last Updated', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</tr></thead>';
    }

    private function sortableColumn($key, $label) {
        $order = $_GET['order'] ?? 'asc';
        $orderby = $_GET['orderby'] ?? '';
        $next_order = ($orderby === $key && $order === 'asc') ? 'desc' : 'asc';
        $sorted_class = ($orderby === $key) ? "sorted $order" : '';
    
        $url = add_query_arg(['orderby' => $key, 'order' => $next_order]);
    
        echo '<th scope="col" class="manage-column column-' . esc_attr($key) . ' sortable ' . esc_attr($sorted_class) . '">';
        echo '<a href="' . esc_url($url) . '">';
        echo '<span>' . esc_html($label) . '</span>';
        echo '<span class="sorting-indicator"></span>'; // WordPress will handle the icon
        echo '</a>';
        echo '</th>';
    }
    

    private function tableBody()
    {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $buddybots = $this->sql->getAllChatbots();
        echo '<tbody>';
        if (!empty($buddybots)) {
            foreach ($buddybots as $buddybot) {
                $edit_url = get_admin_url() . 'admin.php?page=buddybot-chatbot&chatbot_id=' . intval($buddybot['id']);
                $delete_url = '#';
                
                echo '<tr class="buddybot-chatbot-table-row">';
                echo '<td class="column-primary">';
                echo '<strong><a class="row-title" href="' . esc_url($edit_url) . '">' . esc_html($buddybot['chatbot_name']) . '</a></strong>';    

                // WordPress hover actions (Edit | Delete)
                echo '<div class="row-actions">';
                echo '<span class="edit"><a href="' . esc_url($edit_url) . '">' . __('Edit', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a> | </span>';
                echo '<span class="delete"><a href="' . esc_url($delete_url) . '" class="delete-chatbot" data-id="' . intval($buddybot['id']) . '">' . __('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a></span>';
                echo '</div>';

                echo '</td>';
                echo '<td class="column-description">' . esc_html($buddybot['assistant_id']) . '</td>';
                echo '<td class="column-description">' . esc_html($buddybot['assistant_id']) . '</td>';
                echo '<td class="column-shortcode">' . esc_html('[buddybot_chat id=' . esc_attr($buddybot['id']) . ']') . '</td>';
                echo '<td class="column-created">' . esc_html(get_date_from_gmt($buddybot['created_on'], $date_format . ' ' . $time_format)) . '</td>';
                echo '<td class="column-updated">' . esc_html(get_date_from_gmt($buddybot['edited_on'], $date_format . ' ' . $time_format)) . '</td>';

                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">' . __('No BuddyBots found.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</td></tr>';
        }
        echo '</tbody>';
    }

    private function tableFooter()
    {
        echo '<tfoot><tr>';
        $this->sortableColumn('name', __('BuddyBot Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '<th scope="col" class="manage-column column-modal">' . esc_html(__('Assistant Model', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th scope="col" class="manage-column column-shortcode">' . esc_html(__('Shortcode', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        $this->sortableColumn('created', __('Date Created', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        $this->sortableColumn('updated', __('Last Updated', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '<th scope="col" class="manage-column column-buttons"></th>';
        echo '</tr></tfoot>';
    }

    private function pagination()
    {
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo '<span class="pagination-links">';
        echo '<a class="first-page button" href="#">&laquo;</a>';
        echo '<a class="prev-page button" href="#">&lsaquo;</a>';
        echo '<span class="paging-input">1 of 5</span>';
        echo '<a class="next-page button" href="#">&rsaquo;</a>';
        echo '<a class="last-page button" href="#">&raquo;</a>';
        echo '</span>';
        echo '</div>';
        echo '</div>';
    }


}