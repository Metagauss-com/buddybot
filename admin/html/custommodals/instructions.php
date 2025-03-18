<?php

namespace BuddyBot\Admin\Html\CustomModals;

class Instructions extends \BuddyBot\Admin\Html\CustomModals\MoRoot
{
    protected $modal_id = 'buddybot-sample-instructions-modal';
    protected $close_outside = 'data-close-outside';

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
                __('If the user asks for a summary of the event, provide them with key details like the date, time, and location.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Be concise but informative. Always provide just enough detail to answer the question, without overwhelming the user.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Use a friendly and approachable tone when explaining registration steps.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('If the user is looking for an event summary, provide a brief but engaging description of the event.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Do not provide external links unless explicitly asked for by the user.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('For ticketing inquiries, guide the user through available options with clear steps.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Encourage users to book tickets early for popular events.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Provide alternative ways to reach customer support if the assistant cannot resolve the issue.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('If asked about event pricing, give a clear breakdown of costs, discounts, and payment methods.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                __('Always provide event details clearly, ensuring the user understands ticket types, prices, and dates.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            ];

            foreach ($instructions as $instruction) {
                ?>
                <div class="buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-list-group-item">
                    <span><?php echo esc_html($instruction); ?></span>
                    <button class="buddybot-btn buddybot-btn-light buddybot-btn-sm copy-btn" data-text="<?php echo esc_attr($instruction); ?>">
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

   

    protected function showCloseButton()
    {
        return true;
    }

    protected function modalSize()
    {
        return 'buddybot-modal-lg';
    }

}