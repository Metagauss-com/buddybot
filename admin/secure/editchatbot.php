<?php

namespace BuddyBot\Admin\Secure;

final class EditChatBot extends \BuddyBot\Admin\Secure\MoRoot
{
    public function buddybotId()
    {
        $id = isset($_POST['buddybot_data']['buddybot_id']) ? absint($_POST['buddybot_data']['buddybot_id']) : 0;

        if ($id === 0) {
            return false;
        }

        $chatbot = $this->sql->getItemById('chatbot', $id);
        if ($chatbot === null) {
            $id = false;
        }

        return $id;
    }

    public function buddybotName()
    {
        $name = isset($_POST['buddybot_data']['buddybot_name']) ? sanitize_text_field($_POST['buddybot_data']['buddybot_name']) : '';

        if (empty($name)) {
            $this->errors[] = __('BuddyBot name cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (strlen($name) > 256) {
            $this->errors[] = __('BuddyBot name cannot be more than 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (!preg_match('/^[\w\s\-]+$/u', $name)) {
            $this->errors[] = __('BuddyBot name contains invalid characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        return $name;
    }

    public function buddybotDescription()
    {
        $description = isset($_POST['buddybot_data']['buddybot_description']) ? sanitize_textarea_field(wp_unslash($_POST['buddybot_data']['buddybot_description'])) : '';

        if (strlen($description) > 1024) {
            $this->errors[] =  __('BuddyBot description cannot be more than 1024 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $description;
    }

    public function assistantName()
    {
        $name = isset($_POST['buddybot_data']['assistant_name']) ? sanitize_text_field($_POST['buddybot_data']['assistant_name']) : '';

        if (empty($name)) {
            $this->errors[] = __('Assistant name cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (strlen($name) > 256) {
            $this->errors[] = __('Assistant name cannot be more than 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (!preg_match('/^[\w\s\-]+$/u', $name)) {
            $this->errors[] = __('Assistant name contains invalid characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        return $name;
    }

    public function assistant_model()
    {
        return isset($_POST['buddybot_data']['assistant_model']) ? sanitize_text_field($_POST['buddybot_data']['assistant_model']) : '';
    }

    public function additionalInstructions()
    {
        $instructions = isset($_POST['buddybot_data']['additional_instructions']) ? sanitize_textarea_field(wp_unslash($_POST['buddybot_data']['additional_instructions'])) : '';

        if (strlen($instructions) > 32768) {
            $this->errors[] = __('Additional Instructions cannot exceed 32,768 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $instructions;
    }

    public function assistantTemperature()
    {
        $temperature = isset($_POST['buddybot_data']['assistant_temperature']) ? floatval($_POST['buddybot_data']['assistant_temperature']) : 0.7;
        if ($temperature < 0.0 || $temperature > 2.0) {
            $this->errors[] = __('Response Temperature must be between 0.0 and 2.0.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $temperature;
    }

    public function topP()
    {
        $top_p = $top_p = isset($_POST['buddybot_data']['assistant_topp']) ? floatval($_POST['buddybot_data']['assistant_topp']) : 1.0;

        if ($top_p < 0.0 || $top_p > 1.0) {
            $this->errors[] = __('Top-p value must be between 0.0 and 1.0.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $top_p;
    }

    public function openAiSearch()
    {
        return isset($_POST['buddybot_data']['openai_search']) ? (bool) $_POST['buddybot_data']['openai_search'] : false;
    }

    public function openAiSearchMsg()
    {
        if (!$this->openAiSearch()) {
            return '';
        }

        $fallback_msg = isset($_POST['buddybot_data']['openaisearch_msg']) ? wp_unslash(sanitize_text_field($_POST['buddybot_data']['openaisearch_msg'])) : '';

        if (empty($fallback_msg)) {
            $this->errors[] = __('Fallback msg cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $fallback_msg;
        }

        if (strlen($fallback_msg) > 512) {
            $this->errors[] = __('Fallback msg cannot be more than 512 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $fallback_msg;
        }

        return $fallback_msg;
    }

    public function personalizedOptions()
    {
        return isset($_POST['buddybot_data']['personalized_options']) ? (bool) $_POST['buddybot_data']['personalized_options'] : false;
    }

    public function fallbackBehavior()
    {
        $valid_options = ['ask', 'generic', 'escalate'];
        $fallback = isset($_POST['buddybot_data']['fallback_behavior']) ? sanitize_text_field($_POST['buddybot_data']['fallback_behavior']) : 'ask';

        if (!in_array($fallback, $valid_options, true)) {
            $this->errors[] = __('Invalid Fallback Behavior selected.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $fallback;
    }

    public function emotionDetection()
    {
        return isset($_POST['buddybot_data']['emotion_detection']) ? (bool) $_POST['buddybot_data']['emotion_detection'] : false;
    }

    public function greetingMessage()
    {
        $greeting =  isset($_POST['buddybot_data']['greeting_message']) ? sanitize_text_field($_POST['buddybot_data']['greeting_message']) : '';

        if (strlen($greeting) > 256) {
            $this->errors[] = __('Greeting message cannot exceed 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return $greeting;
    }

    public function multilingualSupport()
    {
        return isset($_POST['buddybot_data']['multilingual_support']) ? (bool) $_POST['buddybot_data']['multilingual_support'] : false;
    }

    public function vectorstoreId()
    {
        return isset($_POST['buddybot_data']['vectorstore_id']) && !empty($_POST['buddybot_data']['vectorstore_id']) ? sanitize_text_field($_POST['buddybot_data']['vectorstore_id']) : '';
    }

    public function assistantId()
    {
        return isset($_POST['buddybot_data']['assistant_id']) && !empty($_POST['buddybot_data']['assistant_id']) ? sanitize_text_field($_POST['buddybot_data']['assistant_id']) : '';
    }

    public function dataErrors()
    {
        return $this->errors;
    }
}