<?php

namespace BuddyBot\Admin\Html\Views;

class ViewConversation extends \BuddyBot\Admin\Html\Views\MoRoot
{
    protected $heading;

    protected function setHeading()
    {
            $this->heading = __('Conversation', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    public function getHtml()
    {
        $this->deleteConversationModal();
        $this->alertContainer();
        // $this->conversationsBtnsOutside();
        $this->showRelatedConversationMsg();
        $this->conversationBox();
    }

    private function conversationBox()
    {
        echo '<div class="container-fluid mt-4">';
        echo '<div class="row justify-content-center">';
        echo '<div class="col-md-12">';
        echo '<div class="row border small bg-white">';
        $this->conversationInside();
        $this->messagesListContainer();
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function deleteConversationModal()
    {
        echo '<div class="modal fade" id="buddybot-delete-viewconversation-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="buddybot-delete-viewconversation-modal-label" aria-hidden="true">';
        echo '<div class="modal-dialog modal-dialog-centered">';
        echo '<div class="modal-content">';
        echo '<div class="modal-header">';
        echo '<h1 class="modal-title fs-5" id="buddybot-delete-viewconversation-modal-label">' . esc_html__('Confirm Deletion', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</h1>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo esc_html__('Are you sure you want to delete this Conversation? This action cannot be undone.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';
        echo '<div class="modal-footer">';
        echo '<span id="buddybot-deleting-viewconversation-msg" class="text-danger small" style="display: none;">Deleting...</span>';
        echo '<button type="button" class="btn btn-secondary" id="buddybot-delete-viewconversation-cancel-btn" data-bs-dismiss="modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '<button type="button" class="btn btn-danger" id="buddybot-confirm-viewconversation-delete-btn">' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '</div> </div> </div> </div>';
    }

    private function conversationBtns()
    {
        $user_id = '';
        if (!empty($_GET['user_id'])) {
            $user_id = sanitize_text_field($_GET['user_id']);
        }

        echo '<div class="bb-submit bb-conversation-btns d-flex align-items-center">';
        echo '<a href="' . admin_url('admin.php?page=buddybot-conversations') . '" ';
        echo 'class="me-3 text-decoration-none" role="button"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/>
</svg>' . esc_html__('Back', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>';

        // echo '<a href="' . admin_url('admin.php?page=buddybot-conversations&filter=true&user_id=' . $user_id) . '" ';
        // echo 'class="button button-primary me-2" role="button">' . esc_html__('Filter', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>';

        echo '<input type="submit" id="buddybot-conversation-delete-btn" ';
        echo 'class="button button-primary me-2" value="' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        echo '</div>';

        echo '<button type="button" class="button button-primary me-2" disabled id="buddybot-related-conversation-btn" onclick="location.href=\'' . admin_url('admin.php?page=buddybot-conversations&filter=true&user_id=' . $user_id) . '\';">';
        echo esc_html__('Related Conversation', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
        echo '<span id="buddybot-related-conversation-count" style="display: none;"></span>';
        
        echo '<button type="button" class="button button-primary me-2" disabled id="buddybot-past-conversation-btn">';
        echo esc_html__('Load Previous chat', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
        echo '<span id="buddybot-past-conversation-spinner" class="spinner is-active" style="display:none;" aria-hidden="true"></span>';
    }

    private function showRelatedConversationMsg()
    {
        echo '<div id="buddybot-related-conversation-msg-container">';
        echo '<p class=" text-start" id="buddybot-related-conversation-msg"></p>';
        echo '</div>';
    }

//     private function conversationBtnInside()
// {
//     echo '<div class="container">';
    
//     // Row for header, buttons and other content in a single line
//     echo '<div class="row align-items-center justify-content-between border-bottom">';

//     // Left section for buttons (Code)
//     echo '<div class="col-auto">';
//     echo '<div id="buddybot-playground-thread-operations" class="d-flex p-3">';
//     echo '<button id="buddybot-playground-past-messages-btn" type="button" class="btn btn-outline-dark btn-sm" style="opacity:50;">';
//     $this->moIcon('arrow_upward');
//     echo '</button>';
//     echo '</div>';
//     echo '</div>';

//     // Center section for the heading
//     echo '<div class="col text-center">';
//     $this->pageHeading($this->heading);
//     echo '</div>';

//     // Right section for any additional buttons or content (optional)
//     echo '<div class="col-auto">';
//     $this->conversationBtnsOutside();
//     echo '</div>';

//     echo '</div>';
//     echo '</div>';
// }

    private function conversationInside()
    {
        echo '<div id="buddybot-conversation-thread-operations" class="d-flex d-none p-3">';
        echo '<input id="buddybot-conversation-first-message-id" type="hidden">';
        echo '<input id="buddybot-conversation-last-message-id" type="hidden">';
        echo '<input id="buddybot-conversation-has-more-messages" type="hidden">';
        echo '</div>';
        echo '<div class="container">';
        
        echo '<div class="row align-items-center justify-content-between border-bottom">';

        // Left section for the heading
        echo '<div class="col-12 text-left border-bottom bg-light py-3 d-none">';
        echo '<div class="mb-0">';
        echo '<h3 class="mb-0 bb-conversation-heading">';
        echo esc_html($this->heading);
        echo '</h3>';
        echo '</div>';
        echo '</div>';

        // Right section for the function or buttons
        echo '<div class="col-12 d-flex align-items-center py-3">';
        $this->conversationBtns();
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    private function messagesListContainer()
    {
        echo '<div id="buddybot-conversation-messages-list" class="p-3 position-relative" style="overflow-y: auto; min-height: 200px; max-height: 65vh;">';
        
        // echo '<button id="buddybot-conversation-past-messages-btn" type="button" class="btn btn-outline-dark btn-sm" style="display: none;" title="' . esc_attr(__('View past messages', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '">';
        // $this->moIcon('arrow_upward');
        // echo '</button>';
        echo '</div>';
        echo '<div id="buddybot-conversation-loading-spinner" class="spinner-border text-dark d-flex position-absolute top-50 start-50 mx-auto" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
    }
}