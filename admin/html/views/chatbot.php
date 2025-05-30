<?php

namespace BuddyBot\Admin\Html\Views;

final class ChatBot extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $this->alertContainer();
        echo '<div class="wrap">';
        $heading = __('BuddyBots', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->customPageHeading($heading);
        $this->assistantsTable();
        $this->buddybotFooterBanner();
        echo '</div>';
        $this->pageModals();
        $this->toastContainer();
    }

    protected function pageModals()
    {
        $deleteBuddybot = new \BuddyBot\Admin\Html\CustomModals\DeleteChatBot();
        $deleteBuddybot->getHtml();
    }

    protected function customPageHeading($heading)
    {
        $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        echo '<div class="buddybot-header-wrap">';
        echo '<div class="buddybots-page-heading">';
        echo '<h1 class="wp-heading-inline">';
       // echo esc_html($heading);
        echo '</h1>';
        
        echo '<hr class="wp-header-end">';
        echo '<h2 class="screen-reader-text">Filter pages list</h2>';
        
        echo '<div class="bb-top-head-section">';
        $this->documentationContainer('https://getbuddybot.com/what-are-assistants-and-how-to-create-them/');
        echo '<h1 class="wp-heading-inline">';
        echo esc_html($heading);
        echo '</h1>';
         $this->pageBtns();
        //echo '<a href="#" class="page-title-action">Add New</a>';
        echo '</div>';
        
       
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
        $add_assistant_page = get_admin_url() . 'admin.php?page=buddybot-editchatbot';
        echo '<a href="' . esc_url($add_assistant_page) . '" class="page-title-action">';
        echo esc_html(__('Add New', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</a>';
    }

    private function paginationDropdown()
    {
        
        $saved_value = esc_attr(get_option('buddybot_columns_per_page', 10));

        echo '<div id="buddybot-chatbot-dropdown">';
        echo '<label>' . esc_html__('Results per page →', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label>';
        echo '<select id="buddybot-chatbot-pagination">';
        
        $options = [10, 20, 30, 40, 50];
        
        foreach ($options as $option) {
            $option_value = esc_attr($option);
            $selected = ($saved_value == $option_value) ? 'selected' : '';
            // Translators: %s represents the page number.
            echo '<option value="' . esc_attr($option_value) . '" ' . esc_attr($selected) . '>' . sprintf(esc_html__('page 1-%s', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_html($option_value)) . '</option>';
        }
        
        echo '</select>';
        echo '</div>';
    }

    private function assistantsTable()
    {      
        if (class_exists('\BuddyBot\Admin\Html\Views\Tables\ChatBot')) {
            $buddybots_table = new \BuddyBot\Admin\Html\Views\Tables\ChatBot();

            $buddybots_table->prepare_items();

            $buddybots_table->views();
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="' . esc_attr($_GET['page'] ?? '') . '">';

            $buddybots_table->search_box(__('Search', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), 's');
            $buddybots_table->display();

            echo '</form>';
        } else {
            echo '<p>Error: Class BuddyBot not found!</p>';
        }

    }  
}