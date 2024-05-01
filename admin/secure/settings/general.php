<?php

namespace MetagaussOpenAI\Admin\Secure\Settings;

final class General extends \MetagaussOpenAI\Admin\Secure\MoRoot
{
    protected function cleanOpenAiApiKey($key)
    {
        if ( preg_match('/\s/',$key) ){
            $this->errors[] = __('API Key cannot have white space.', 'metagauss-openai');
            return;
         }

         return sanitize_text_field($key);
    }
}