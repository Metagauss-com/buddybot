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
}