<?php

namespace BuddyBot\Admin\Secure;

final class Chatbot extends \BuddyBot\Admin\Secure\MoRoot
{
    public function chatbotId()
    {
        $id = absint($_POST['chatbot_data']['id']);

        if ($id === 0) {
            return false;
        }

        $chatbot = $this->sql->getItemById('chatbot', $id);
        if ($chatbot === null) {
            $id = false;
        }

        return $id;
    }

    public function chatbotName()
    {
        $name = sanitize_text_field($_POST['chatbot_data']['name']);

        if (empty($name) and !empty($_POST['chatbot_data']['name'])) {
            $this->errors[] = __('Invalid Chatbot name.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (empty($name)) {
            $this->errors[] = __('Chatbot name cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (strlen($name) > 1024) {
            $this->errors[] = __('Chatbot name cannot be more than 1024 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        return $name;
    }

    public function chatbotDescription()
    {
        $description = sanitize_textarea_field(wp_unslash($_POST['chatbot_data']['description']));

        if (strlen($description) > 2048) {
            $this->errors[] =  __('Chatbot description cannot be more than 2048 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $description;
    }

    public function chatbotAssistantId()
    {
        $assistant_id = sanitize_text_field($_POST['chatbot_data']['assistant_id']);

        if (empty($assistant_id)) {
            $this->errors[] = __('Please select an Assistant for this Chatbot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $assistant_id;
    }

    public function dataErrors()
    {
        return $this->errors;
    }
}