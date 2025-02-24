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
        echo '<label>' . esc_html__('Results per page →', 'multiple-buddybots') . '</label>';
        echo '<select id="buddybot-conversation-pagination">';
        
        $options = [10, 20, 30, 40, 50];
        
        foreach ($options as $option) {
            $option_value = esc_attr($option);
            $selected = ($saved_value == $option_value) ? 'selected' : '';
            echo '<option value="' . $option_value . '" ' . esc_attr($selected) . '>' . esc_html__('page 1-' . $option_value, 'multiple-buddybots') . '</option>';
        }
        
        echo '</select>';
        echo '</div>';
    }

    private function conversationsTable()
    {
        // echo '<div class="table-responsive">';
        // echo '<table class="buddybot-org-conversations-table table">';
        // $this->tableHeader();
        // $this->tableBody();
        // echo '</table>';
        // echo '</div>';

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
            echo '<p> '. esc_html__("Error: Class BbTable not found!", "buddybot-ai-custom-ai-assistant-and-chat-agent") .'</p>';
        }
    }

    private function tableHeader()
    {
        echo '<thead>';
        echo '<tr>';
        echo '<th class="col buddybot-conversation-no" scope="col">' . esc_html(__('No.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-conversation-name" scope="col">' . esc_html(__('Thread Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-conversation-User" scope="col">' . esc_html(__('User', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-conversation-date" scope="col">' . esc_html(__('Created', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-conversation-btn" scope="col"></th>';
        echo '</tr>';
        echo '</thead>';
    }

    private function tableBody()
    {
        echo '<tbody>';
        echo '<tr id="buddybot-conversations-loading-spinner">';
        echo '<td colspan="6" class="p-5">';
        echo '<div class="spinner-border text-dark d-flex justify-content-center mx-auto" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
        echo '</td>';
        echo '</tbody>';
    }

    private function noMoreConversations()
    {
        echo '<div id="buddybot-conversations-no-more" class="text-center small fw-bold visually-hidden">';
        esc_html_e('There are no more Conversations to load.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->moIcon('sentiment_satisfied');
        echo '</div>';
    }

    private function loadMoreBtn()
    {
        echo '<div class="text-center">';
        $this->loaderBtn('outline-dark btn-sm visually-hidden', 'buddybot-conversations-load-more-btn', __('Load More', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</div>';
    }

    private function deleteConversationModal()
    {
        echo '<div class="modal fade" id="buddybot-delete-conversation-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="buddybot-delete-confirmation-modal-label" aria-hidden="true">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h1 class="modal-title fs-5" id="buddybot-delete-conversation-modal-label">' . esc_html__('Confirm Deletion', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</h1>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo esc_html__('Are you sure you want to delete this Conversation? This action cannot be undone.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<span id="buddybot-deleting-conversation-msg" class="text-danger small" style="display: none;">Deleting...</span>';
        echo '<button type="button" class="btn btn-secondary" id="buddybot-delete-conversation-cancel-btn" data-bs-dismiss="modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '<button type="button" class="btn btn-danger" id="buddybot-confirm-conversation-delete-btn">' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '</div> </div> </div> </div>';
    }
}