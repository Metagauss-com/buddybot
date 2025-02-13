<?php

namespace BuddyBot\Admin\Html\Modals;

class DeleteBuddyBot extends \BuddyBot\Admin\Html\Modals\MoRoot
{
    protected $modal_id = 'buddybot-delete-confirmation-modal';

    protected function modalTitle()
    {
        return __('Confirm Deletion', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function bodyContent()
    {
        echo esc_html__('Are you sure you want to delete this BuddyBot? This action cannot be undone.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function footerContent()
    {
        echo '<span id="buddybot-deleting-msg" class="text-danger small" style="display: none;">Deleting...</span>';
        echo '<button type="button" class="btn btn-secondary btn-sm" id="buddybot-cancel-delete-btn" data-bs-dismiss="modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '<button type="button" class="btn btn-danger btn-sm" id="buddybot-confirm-delete-btn">' . esc_html__('Delete', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
    }

}