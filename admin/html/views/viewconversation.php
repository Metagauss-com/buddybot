<?php

namespace BuddyBot\Admin\Html\Views;

class ViewConversation extends \BuddyBot\Admin\Html\Views\MoRoot
{
    // protected $thread_id;
    // protected $user_id;
    protected $heading;

    // protected function setconversationId()
    // {
    //     if (!empty($_GET['thread_id'])) {
    //         $this->thread_id = sanitize_text_field($_GET['thread_id']);
    //     }

    //     if (!empty($_GET['user_id'])) {
    //         $this->user_id = sanitize_text_field($_GET['user_id']);
    //     }
    // }

    protected function setHeading()
    {
            $this->heading = esc_html(__('Conversation', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
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

        echo '<div class="submit bb-conversation-btns">';
        echo '<a href="' . admin_url('admin.php?page=buddybot-conversations') . '" ';
        echo 'class="button button-primary me-2" role="button">' . esc_html__('Back', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>';

        echo '<a href="' . admin_url('admin.php?page=buddybot-conversations&filter=true&user_id=' . $user_id) . '" ';
        echo 'class="button button-primary me-2" role="button">' . esc_html__('Filter', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>';

        echo '<input type="submit" id="buddybot-conversation-delete-btn" ';
        echo 'class="button button-primary me-2" value="' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        echo '</div>';
    }

    private function showRelatedConversationMsg()
    {
        echo '<div>';
        echo '<p class=" text-start mt-3" id="buddybot-related-conversation-msg"></p>';
        echo '<div id="buddybot-conversation-loading-spinner" class="spinner-border text-dark d-flex justify-content-center mx-auto" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
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
        echo '<div id="buddybot-conversation-thread-operations" class="d-flex p-3">';
        echo '<input id="buddybot-conversation-first-message-id" type="hidden">';
        echo '<input id="buddybot-conversation-last-message-id" type="hidden">';
        echo '<input id="buddybot-conversation-has-more-messages" type="hidden">';
        echo '<button id="buddybot-conversation-past-messages-btn" type="button" class="btn btn-outline-dark btn-sm" style="opacity:50;">';
        $this->moIcon('arrow_upward');
        echo '</button>';
        echo '</div>';
        echo '<div class="container">';
        
        echo '<div class="row align-items-center justify-content-between border-bottom">';

        // Left section for the heading
        echo '<div class="col text-left">';
        $this->pageHeading($this->heading);
        echo '</div>';

        // Right section for the function or buttons
        echo '<div class="col-auto">';
        $this->conversationBtns();
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    private function messagesListContainer()
    {
        echo '<div id="buddybot-conversation-messages-list" class="p-3" style="overflow-y: auto; max-height: 400px;">';
        echo '</div>';
    }
}