<?php

namespace BuddyBot\Admin\Html\Views;

final class PluginFeedback extends \BuddyBot\Admin\Html\Views\MoRoot
{

    public function getHtml()
    {
        $this->feedbackModal();
    }

    private function feedbackModal()
    {
        echo '<div class="buddybot-modal" tabindex="-1" id="buddybot-deactivation-feedback" data-close-outside>';
        echo '<div class="buddybot-modal-dialog buddybot-feedback-modal">';
        echo '<div class="buddybot-modal-content">';
        $this->feedbackModalHeader();
        $this->feedbackModalBody();
        $this->feedbackModalFooter();
        echo '</div></div></div>';
    }

    private function feedbackModalHeader()
    {
        echo '<div class="buddybot-modal-header buddybot-ps-0">';
        echo '<div class="buddybot-modal-title buddybot-fs-6 buddybot-text-dark buddybot-ms-3">';
        echo esc_html__('Uninstalling Buddybot?', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';
        echo '<button type="button" class="buddybot-close-btn" data-modal="buddybot-deactivation-feedback" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="#1f1f1f"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
        </button>';
        echo '</div>';
    }

    private function feedbackModalBody()
    {
        echo '<div class="buddybot-modal-body buddybot-pt-0">';
        echo '<div class="buddybot-mb-2">';
        echo '<div id="buddybot-plugin-deactivation-alert" class="buddybot-text-danger buddybot-mb-2 buddybot-text-small " style="display:none">';
        echo esc_html__('Please let us know why you\'re deactivating BuddyBot, or select the temporary deactivation option if you\'d like to try it later.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';
        echo '<div class="buddybot-mb-4 buddybot-fs-3 buddybot-text-dark">' . esc_html__('Maybe we can convince you to return!', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        echo '<div class="buddybot-fs-6 buddybot-pb-2 ">' . esc_html__('Let us know what went wrong and how we can fix it?', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</div>';
        echo '<textarea id="buddybot-feedback-message" name="buddybot-feedback-message" class="buddybot-box-w-100" rows="3" cols="50"></textarea>';
        echo '<div class="buddybot-checkbox buddybot-pt-1">';
        echo '<input type="checkbox" id="buddybot-temp-deactivate" name="buddybot-temp-deactivate">';
        echo '<label for="buddybot-temp-deactivate" class="buddybot-text-small">' . esc_html__('This is a temporary deactivation', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    private function feedbackModalFooter()
    {
        echo '<div class="buddybot-modal-footer buddybot-border-top buddybot-justify-content-between">';
        echo '<button type="button" class="button button-secondary" id="buddybot-plugin-feedback-direct-deactivation">';
        echo esc_html__('Skip & Deactivate', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
        echo '<div id="buddybot-plugin-deactivation-loader" class="buddybot-mb-2 " style="display:none">';
        echo esc_html__('Deactivating...', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<span class="spinner is-active" aria-hidden="true"></span>';
        echo '</div>';
        echo '<button type="submit" class="button button-primary" id="buddybot-plugin-feedback-deactivation">';
        echo esc_html__('Submit & Deactivate', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
        echo '</div>';
    }
    
}