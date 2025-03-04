<?php

namespace BuddyBot\Admin\Html\CustomModals;

class ChangeKey extends \BuddyBot\Admin\Html\CustomModals\MoRoot
{
    protected $modal_id = 'buddybot-changekey-modal';

    protected function modalTitle()
    {
        return __('Change OpenAI API Key', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function bodyContent()
    {
        echo esc_html__('Changing your OpenAI API key may impact your existing setup. If the new key belongs to a different OpenAI project, your assistants, vector stores, AI training data, and conversation history will not be accessible. Are you sure you want to proceed?', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function footerContent()
    {
        echo '<button type="button" class="btn btn-secondary btn-sm" id="buddybot-change-key-cancel-btn" data-modal="buddybot-changekey-modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        echo '<button type="button" class="btn btn-primary btn-sm" id="buddybot-change-key-confirm-btn">' . esc_html__('Confirm & Edit', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
    }

}