<?php

namespace MetagaussOpenAI\Admin\Secure;

final class Chatbot extends \MetagaussOpenAI\Admin\Secure\MoRoot
{
    public function chatbotId()
    {
        $id = absint($_POST['chatbot_data']['id']);

        if ($id > 0) {
            $this->errors[] = __('Chatbot name cannot be empty.', 'metagauss-openai');
        }

        if (strlen($name) > 1024) {
            $this->errors[] = __('Chatbot name cannot be more than 1024 characters.', 'metagauss-openai');
        }

        return $name;
    }

    public function chatbotName()
    {
        $name = sanitize_text_field($_POST['chatbot_data']['name']);

        if (empty($name)) {
            $this->errors[] = __('Chatbot name cannot be empty.', 'metagauss-openai');
        }

        if (strlen($name) > 1024) {
            $this->errors[] = __('Chatbot name cannot be more than 1024 characters.', 'metagauss-openai');
        }

        return $name;
    }

    public function chatbotDescription()
    {
        $description = sanitize_textarea_field($_POST['chatbot_data']['description']);

        if (strlen($description) > 2048) {
            $this->errors[] =  __('Chatbot description cannot be more than 2048 characters.', 'metagauss-openai');
        }

        return $description;
    }

    public function chatbotAssistantId()
    {
        $assistant_id = sanitize_text_field($_POST['chatbot_data']['assistant_id']);

        if (empty($assistant_id)) {
            $this->errors[] = __('Please select an Assistant for this Chatbot.', 'metagauss-openai');
        }

        return $assistant_id;
    }

    public function dataErrors()
    {
        return $this->errors;
    }
}