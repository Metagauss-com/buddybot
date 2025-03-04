<?php

namespace BuddyBot\Admin\Html\CustomModals;

class Welcome extends \BuddyBot\Admin\Html\CustomModals\MoRoot
{
    protected $modal_id = 'buddybot-del-confirmation-modal';

    protected function bodyContent()
    {
        $welcomeImage = plugin_dir_url(dirname(__FILE__, 2)) . 'html/images/bb-welcome-image.png';
        $buddybotModalHeading = esc_html__('Welcome to BuddyBot! ', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="buddybot-d-flex buddybot-align-items-center buddybot-p-4">';
        echo ' <div class="bb-modal-image">';
        echo ' <img src="' . esc_url($welcomeImage) . '" alt="BuddyBot Welcome" class="bb-image">';
        echo ' </div>';

        echo ' <div class="bb-modal-text">';
        echo ' <h1 class="bb-modal-title">' . esc_html($buddybotModalHeading) . '</h1>';
        echo ' <p class="bb-modal-description">' . esc_html__("BuddyBot is built to provide direct, AI-driven support to your website visitors. It uses your WordPress content to interact with users on your site, making your website more helpful and interactive. Let's set up your first BuddyBot to enhance the frontend user experience!", "buddybot-ai-custom-ai-assistant-and-chat-agent") . '</p>';
        echo ' <div class="bb-modal-actions">';
        echo ' <button type="button" class="button button-seconday bb-dismiss-modal" data-bs-dismiss="modal">' .esc_html__('Close ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button> ';
        echo ' <button type="button" class="button button-primary bb-get-started">' .esc_html__('Get Started ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button> ';
        echo ' </div>';
        echo ' </div>';
        echo ' </div>';
    }

    protected function modalSize()
    {
        return 'buddybot-modal-lg';
    }
}