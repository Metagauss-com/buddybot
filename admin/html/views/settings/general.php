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
}