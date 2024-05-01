<?php

namespace MetagaussOpenAI\Admin\Html\Views\Settings;

class General extends \MetagaussOpenAI\Admin\Html\Views\Settings\MoRoot
{
    public function getHtml()
    {
        $html = '';
        $html .= $this->openaiApiKey();
        return $html;
    }

    private function openaiApiKey()
    {
        $id = 'mgoa-settings-openai-api-key';
        $label = __('OpenAI API Key', 'metagauss-openai');
        $value = $this->sql->getOption('openai_api_key', 'default');
        $control = '<input type="text" id="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text">';
        $description = __('Your OPENAI API key.', 'metagauss-openai');

        return $this->optionHtml($id, $label, $control, $description);
    }
}