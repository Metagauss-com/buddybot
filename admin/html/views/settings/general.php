<?php

namespace BuddyBot\Admin\Html\Views\Settings;

class General extends \BuddyBot\Admin\Html\Views\Settings\MoRoot
{
    public function getHtml()
    {
        $html = '';
        $html .= $this->openaiApiKey();
        return $html;
    }

    private function openaiApiKey()
    {
        $id = 'buddybot-settings-openai-api-key';
        $label = __('OpenAI API Key', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('openai_api_key', '');
        $control = '<input type="text" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text">';
        $description = __('Your OPENAI API key.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description);
    }
}