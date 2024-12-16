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
        $control = '<form>';
        $control .= '<input type="password" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text" autocomplete="off">';
        $control .= '</form>';
        $description = __('Enter your OpenAI API key. This key allows BuddyBot to access OpenAI services.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        return $this->optionHtml($id, $label, $control, $description);
    }
}