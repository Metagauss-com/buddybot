<?php

namespace BuddyBot\Admin\Html\CustomModals;

class Instructions extends \BuddyBot\Admin\Html\CustomModals\MoRoot
{
    protected $modal_id = 'buddybot-sample-instructions-modal';

    protected function modalTitle()
    {
        return __('Sample Instructions for Customizing Your Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
    }

    protected function bodyContent()
    {
        ?>
        <style>
.buddybot-copy-btn .buddybot-copied-text {
    position: absolute;
    top: -25px;
    right: 0;
    background: #000;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    opacity: 0;
    transform: translateY(5px);
    transition: opacity 0.2s ease, transform 0.2s ease;
    pointer-events: none;
}

.buddybot-copy-btn.buddybot-copied .buddybot-copied-text {
    opacity: 1;
    transform: translateY(0);
}
</style>
        <div class="list-group">
            <?php 
            $instructions = [
                __('Use a professional and polite tone when responding to users.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Respond to event-related queries with detailed information about the venue, schedule, and ticketing options.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Always ask for clarification if a user\'s question is vague or incomplete.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Provide quick responses to frequently asked questions about registration procedures.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('In case of uncertainty, suggest reaching out to customer support.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            ];

            foreach ($instructions as $instruction) {
                ?>
                <div class="d-flex justify-content-between align-items-center list-group-item">
                    <span><?php echo esc_html($instruction); ?></span>
                    <button class="buddybot-copy-btn" data-text="<?php echo esc_attr($instruction); ?>">
                    <span class="buddybot-copied-text"><?php esc_html_e("Copied!", "buddybot-ai-custom-ai-assistant-and-chat-agent"); ?></span>
                        <?php $this->moIcon('content_copy'); ?>
                    </button>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    protected function footerContent()
    {
        echo '<button type="button" class="buddybot-btn-dark" data-modal="buddybot-sample-instructions-modal">' . esc_html__('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
    }

    protected function showCloseButton()
    {
        return true;
    }

    protected function modalSize()
    {
        return 'buddybot-modal-lg';
    }

}