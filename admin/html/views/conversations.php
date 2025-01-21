<?php

namespace BuddyBot\Admin\Html\Views;

final class Conversations extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $this->deleteConversationModal();
        $heading = __('Conversations', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->pageHeading($heading);
        $this->pageBtns();
        $this->alertContainer();
        $this->conversationsTable();
        $this->noMoreConversations();
        $this->loadMoreBtn();
    }

    private function pageBtns() {
        $saved_value = get_option('buddybot_conversations_per_page', 10);
        echo '<div id="buddybot-conversation-dropdown" class="mb-3 d-flex justify-content-end">';
        echo '<label class="mb-0 me-2" style="white-space: nowrap;">';
        esc_html_e('Results per page →', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label>';
        echo '<select id="buddybot-conversation-load-more-limit" class="form-select ms-2 w-auto">';
        
        foreach ([10, 20, 30, 40, 50] as $option) {
            echo '<option value="' . esc_attr($option) . '" ' . ($saved_value == $option ? 'selected' : '') . '>';
            echo esc_html__('page 1-' . $option, 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo '</option>';
        }
    
        echo '</select>';
        echo '</div>';
    }

    private function conversationsTable()
    {
        echo '<table class="buddybot-org-conversations-table table table-sm">';
        $this->tableHeader();
        $this->tableBody();
        echo '</table>';
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
        echo '<tr id="buddybot-assistants-loading-spinner">';
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
        echo '<button type="button" class="btn btn-secondary" id="buddybot-delete-conversation-cancel-btn" data-bs-dismiss="modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '<button type="button" class="btn btn-danger" id="buddybot-confirm-conversation-delete-btn">' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '</div> </div> </div> </div>';
    }
}