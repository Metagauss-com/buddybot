<?php

namespace BuddyBot\Admin\Html\Views\Settings;

class General extends \BuddyBot\Admin\Html\Views\Settings\MoRoot
{
    public function getHtml()
    {
        $html = '';
        $html .= $this->openaiApiKey();
        $html .= $this->enableVisitorChat();
        $html .= $this->disableCookies();
        $html .= $this->sessionExpiry();
        $html .= $this->anonymousUserEmail();
        $html .= $this->deleteExpiredChat();
        $html .= $this->ConversationExpiryTime();
        return $html;
    }

    private function openaiApiKey()
    {
        $id = 'buddybot-settings-openai-api-key';
        $label = __('OpenAI API Key', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('openai_api_key', '');
        if(!empty($value)){
            $value = substr($value, 0, 4) . str_repeat('*', strlen($value) - 8) . substr($value, -4);
        }
        $field_disabled = empty($value) ? '' : 'disabled';
        $btn_disabled = empty($value) ? 'disabled' : '';
        $control = '<input type="hidden" id="buddybot-settings-hidden-key" value="' . esc_attr($value) . '">';
        $control .= '<input type="text" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text me-2" autocomplete="off"' . esc_html($field_disabled) .'>';
        $control .= '<button type="button" id="buddybot-settings-key-change-btn" class="button button-primary"' . esc_html($btn_disabled) .'>' . esc_html__('Change Key', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        $description = 
        sprintf(
                __('Enter your OpenAI API key to enable BuddyBot to access services powered by ChatGPT. <a href="%s" target="_blank">Click here to create an OpenAI account</a>. New users receive <em>free credits</em> to explore ChatGPT and other OpenAI services. After signing up, you can generate your API key from the <a href="%s" target="_blank">API keys page</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                esc_url('https://platform.openai.com/signup/'), 
                esc_url('https://platform.openai.com/account/api-keys')
            );

        return $this->optionHtml($id, $label, $control, $description);
    }

    private function enableVisitorChat()
    {
        $id = 'buddybot-settings-enable-visitor-chat';
        $label = __('Enable Visitor Chat', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('enable_visitor_chat', 0);
        $checked = $value === '1' ? 'checked' : '';
        $control = '<input type="checkbox" id="' . esc_attr($id) . '" value="1" ' . esc_attr($checked) . '>';
        $description = __(' Enable this option to allow non-logged-in users (visitors) to interact with BuddyBot. If disabled, only logged-in users will be able to access and use BuddyBot, and visitors will see an error message if they try to interact with it. When enabled, visitors can start a conversation with BuddyBot, and their chat history will be temporarily stored for the session.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description);
    }

    private function sessionExpiry()
    {
        $id = 'buddybot-settings-session-expiry';
        $childfieldrow = ' id="buddybot-visitor-chat-childfieldrow-first" style="display: none;" ';
        $label = __('Visitor Session Expiry Time', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('session_expiry', 24);
        $control = '<input type="number" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text" min="1" max="365" step="1">';
        $description = __('Set the time duration (in hours) after which a visitor\'s conversation will expire. Once the session expires, the chat history will no longer be visible to the visitor.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description, $childfieldrow);
    }

    private function deleteExpiredChat()
    {
        $id = 'buddybot-settings-delete-expired-chat';
        $childfieldrow = 'id="buddybot-visitor-chat-childfieldrow-second" style="display: none;"';
        $label = __('Delete Expired Conversations', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('delete_expired_chat', 0);
        $checked = $value === '1' ? 'checked' : '';
        $control = '<input type="checkbox" id="' . esc_attr($id) . '" value="1" ' . esc_attr($checked) . '>';
        $description = __('Enable this option to automatically delete conversations when a user\'s session cookie expires. A daily cron job will run to remove expired conversations, helping to keep your database clean.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description, $childfieldrow);
    }

    private function disableCookies()
    {
        $id = 'buddybot-settings-disable-cookies';
        $childfieldrow = 'id="buddybot-visitor-chat-childfieldrow-third" style="display: none;"';
        $label = __('Disable Cookies', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('disable_cookies', 0);
        $checked = $value === '1' ? 'checked' : '';
        $control = '<input type="checkbox" id="' . esc_attr($id) . '" value="1" ' . esc_attr($checked) . '>';
        $description = __('Enable this option to prevent BuddyBot from storing any temporary session data (like chat history) on the visitor\'s browser.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description, $childfieldrow);
    }

    private function ConversationExpiryTime()
    {
        $id = 'buddybot-settings-coversation-expiry-time';
        $childfieldrow = ' id="buddybot-delete-expired-chat-childfieldrow" style="display: none;" ';
        $label = __('Visitor Chat Expiry Time', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('conversation_expiry_time', 24);
        $control = '<input type="number" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text" min="1" max="365" step="1">';
        $description = __('Set the time duration (in days) after which a visitor\'s conversation will expire. Once the conversation expires, it will be deleted.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description, $childfieldrow);
    }

    private function anonymousUserEmail()
    {
        $id = 'buddybot-settings-user-email';
        $childfieldrow = 'id="buddybot-visitor-chat-childfieldrow-four" style="display: none;"';
        $label = __('Require Email Before Chat', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('anonymous_user_email', 0);
        $checked = $value === '1' ? 'checked' : '';
        $control = '<input type="checkbox" id="' . esc_attr($id) . '" value="1" ' . esc_attr($checked) . '>';
        $description = __('Ask non-logged-in users to enter their email before starting anonymous chat. Email will not be verified. Intended for lead generation and support follow-ups.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description, $childfieldrow);
    }
}