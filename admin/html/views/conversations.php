<?php

namespace BuddyBot\Admin\Html\Views;

final class Conversations extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $this->alertContainer();
        $heading = __('Conversations', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->customPageHeading($heading);
        $this->conversationsTable();
        $this->toastContainer();
        $this->pageModals();
    }

    protected function customPageHeading($heading)
    {
        $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        echo '<div class="buddybot-header-wrap">';
        echo '<div class="buddybots-page-heading">';
        echo '<h1 class="wp-heading-inline">';
        echo esc_html($heading);
        echo '</h1>';
        //$this->pageBtns();
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

    protected function pageModals()
    {
        $deleteConversation = new \BuddyBot\Admin\Html\CustomModals\DeleteConversation();
        $deleteConversation->getHtml();
    }

    private function paginationDropdown()
    {
        
        $saved_value = esc_attr(get_option('buddybot_conversations_per_page', 10));

        echo '<div id="buddybot-conversation-dropdown">';
        echo '<label>' . esc_html__('Results per page →', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label>';
        echo '<select id="buddybot-conversation-pagination">';
        
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

    private function conversationsTable()
    {

        if (class_exists('\BuddyBot\Admin\Html\Views\Tables\Conversations')) {
            $buddybots_table = new \BuddyBot\Admin\Html\Views\Tables\Conversations();

            $buddybots_table->prepare_items();

            $buddybots_table->views();
            echo '<form method="get">';
            echo '<input type="hidden" name="page" value="' . esc_attr($_GET['page'] ?? '') . '">';

            $buddybots_table->search_box(esc_html__('Search', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), 's');
            $buddybots_table->display();

            echo '</form>';
        } else {
            echo '<p> '. esc_html__("Error: Class Conversations not found!", "buddybot-ai-custom-ai-assistant-and-chat-agent") .'</p>';
        }
    }
}