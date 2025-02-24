<?php

namespace BuddyBot\Admin\Html\CustomModals;

class DeleteConversation extends \BuddyBot\Admin\Html\CustomModals\MoRoot
{
    protected $modal_id = 'buddybot-del-conversation-modal';

    protected function modalTitle()
    {
        return __('Confirm Deletion', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function bodyContent()
    {
        echo esc_html__('Are you sure you want to delete this Conversation? This action cannot be undone.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function footerContent()
    {
        echo '<span id="buddybot-del-msg" class="buddybot-text-danger" style="display: none;">Deleting...</span>';
        echo '<button type="button" class="buddybot-btn-dark" id="buddybot-cancel-del-conversation-btn" data-modal="buddybot-del-conversation-modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '<button type="button" class="buddybot-btn-danger" id="buddybot-confirm-del-conversation-btn">' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
    }

}