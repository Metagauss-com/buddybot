<?php

namespace BuddyBot\Admin\Html\Views\Settings;

class General extends \BuddyBot\Admin\Html\Views\Settings\MoRoot
{
    public function getHtml()
    {
        $html = '';
        $html .= $this->openaiApiKey();
        $html .= $this->deepseekApiKey(); // P589c
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
        $description = 
        sprintf(
                __('Enter your OpenAI API key to enable BuddyBot to access services powered by ChatGPT. <a href="%s" target="_blank">Click here to create an OpenAI account</a>. New users receive <em>free credits</em> to explore ChatGPT and other OpenAI services. After signing up, you can generate your API key from the <a href="%s" target="_blank">API keys page</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                esc_url('https://platform.openai.com/signup/'), 
                esc_url('https://platform.openai.com/account/api-keys')
            );

        return $this->optionHtml($id, $label, $control, $description);
    }

    private function deepseekApiKey() // P589c
    {
        $id = 'buddybot-settings-deepseek-api-key';
        $label = __('DeepSeek API Key', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $value = $this->options->getOption('deepseek_api_key', '');
        $control = '<form>';
        $control .= '<input type="password" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text" autocomplete="off">';
        $control .= '</form>';
        $description = 
        sprintf(
                __('Enter your DeepSeek API key to enable BuddyBot to access services powered by DeepSeek R1. <a href="%s" target="_blank">Click here to create a DeepSeek account</a>. After signing up, you can generate your API key from the <a href="%s" target="_blank">API keys page</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
                esc_url('https://deepseek.com/signup/'), 
                esc_url('https://deepseek.com/account/api-keys')
            );

        return $this->optionHtml($id, $label, $control, $description);
    }
}
