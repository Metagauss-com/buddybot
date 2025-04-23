<?php

namespace BuddyBot\Admin\Responses\Prompt;

class MoRoot extends \BuddyBot\Admin\Responses\MoRoot
{
    protected $error;

    public function generatePrompt($data)
    {
        $prompt_data = array();

        if(!is_array($data)) {
            $this->error = __('Data should be in array format.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        foreach ($data as $name => $value) {
            $method = 'prompt' . str_replace('_','',$name);
            $prompt_data[$name] = $this->$method($value);
        }

        return $prompt_data;
    }

    public function getErrors()
    {
        return $this->error;
    }
}