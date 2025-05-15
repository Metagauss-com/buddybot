<?php

namespace BuddyBot\Admin\Secure\Settings;

final class General extends \BuddyBot\Admin\Secure\MoRoot
{
    protected function cleanOpenAiApiKey($key)
    {
        if ( preg_match('/\s/',$key) ){
            $this->errors[] = __('API Key cannot have white space.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
         }

         return sanitize_text_field($key);
    }

    protected function cleanEnableVisitorChat($value)
    {
        $value = ($value === "1") ? "1" : "0";

        return sanitize_text_field(intval($value));
    }

    protected function cleanSessionExpiry($value)
    {
        if (!is_numeric($value)) {
            $this->errors[] = __('Session expiry time must be a valid number.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        $value = intval($value);

        if ($value <= 0) {
            $this->errors[] = __('Session expiry time must be a positive number greater than zero.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        return $value;
    }

    protected function cleanDeleteExpiredChat($value)
    {
        $value = ($value === "1") ? "1" : "0";

        return sanitize_text_field(intval($value));
    }

    protected function cleanDisableCookies($value)
    {
        $value = ($value === "1") ? "1" : "0";

        return sanitize_text_field(intval($value));
    }

    protected function cleanConversationExpiryTime($value)
    {
        if (!is_numeric($value)) {
            $this->errors[] = __('Conversation expiry time must be a valid number.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        $value = intval($value);

        if ($value <= 0) {
            $this->errors[] = __('Conversation expiry time must be a positive number greater than zero.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        return $value;
    }

    protected function cleanAnonymousUserEmail($value)
    {
        $value = ($value === "1") ? "1" : "0";

        return sanitize_text_field(intval($value));
    }
}