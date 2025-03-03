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
        <div class="buddybot-list-group">
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
                <div class="buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-list-group-item">
                    <span><?php echo esc_html($instruction); ?></span>
                    <button class="buddybot-btn buddybot-btn-light buddybot-btn-sm copy-btn" data-text="<?php echo esc_attr($instruction); ?>">
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