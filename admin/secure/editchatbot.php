<?php

namespace BuddyBot\Admin\Secure;

final class EditChatBot extends \BuddyBot\Admin\Secure\MoRoot
{
    public function cleanBuddybotId($id)
    {  
        $id = absint($id);

        if ($id === 0) {
            return false;
        }

        $chatbot = $this->sql->getItemById('chatbot', $id);
        if ($chatbot === null) {
            $id = false;
        }

        return $id;
    }

    public function cleanBuddybotName($name)
    {
        if (empty($name)) {
            $this->errors[] = __('BuddyBot Name is required.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        if (strlen($name) > 256) {
            $this->errors[] = __('BuddyBot name cannot be more than 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }

        return sanitize_text_field($name);
    }

    public function cleanBuddybotDescription($description)
    {
        if (strlen($description) > 1024) {
            $this->errors[] = __('BuddyBot description cannot be more than 1024 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return sanitize_textarea_field($description);
    }

    public function cleanExistingAssistant($existing_assistant)
    {
        return (bool) $existing_assistant;
    }

    public function cleanConnectAssistant($connect_assistant)
    {
        if (empty($connect_assistant)) {
            $this->errors[] = __('Please select an assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $connect_assistant;
        }

        return sanitize_text_field($connect_assistant);
    }

    public function cleanAssistantName($name)
    {
        if (empty($name)) {
            $this->errors[] = __('Assistant Name is required.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }
    
        if (strlen($name) > 256) {
            $this->errors[] = __('Assistant name cannot be more than 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $name;
        }
    
        return sanitize_text_field($name);
    }

    public function cleanAssistantModel($assistant_model)
    {
        if (empty($assistant_model)) {
            $this->errors[] = __('Assistant Model is required.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return '';
        }
    
        return sanitize_text_field($assistant_model);
    }

    public function cleanAdditionalInstructions($instructions)
    {
        if (strlen($instructions) > 32768) {
            $this->errors[] = __('Additional Instructions cannot exceed 32,768 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return sanitize_textarea_field($instructions);
    }

    public function cleanAssistantTemperature($temperature)
    {
        if ($temperature < 0.0 || $temperature > 2.0) {
            $this->errors[] = __('Response Temperature must be between 0.0 and 2.0.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
        return floatval($temperature);
    }

    public function cleanAssistantTopP($top_p)
    {
        if ($top_p < 0.0 || $top_p > 1.0) {
            $this->errors[] = __('Top-p value must be between 0.0 and 1.0.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    
        return floatval($top_p);
    }

    public function cleanOpenaiSearch($openai_search)
    {
        return (bool) $openai_search;
    }

    public function cleanOpenaiSearchMsg($fallback_msg)
    {
        if (empty($fallback_msg)) {
            $this->errors[] = __('Fallback Message is required.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $fallback_msg;
        }
    
        if (strlen($fallback_msg) > 512) {
            $this->errors[] = __('Fallback Message cannot be more than 512 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return $fallback_msg;
        }
    
        return wp_unslash(sanitize_text_field($fallback_msg));
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

    public function cleanEmotionDetection($emotion_detection)
    {
        return (bool) $emotion_detection;
    }

    public function cleanGreetingMessage($greeting)
    {
        if (strlen($greeting) > 256) {
            $this->errors[] = __('Greeting message cannot exceed 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        return sanitize_text_field($greeting);
    }

    public function cleanVectorstoreId($vectorstore_id)
    {
        if (empty($vectorstore_id)) {
            $this->errors[] = __('Vectorstore ID is required.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return '';
        }
    
        return sanitize_text_field($vectorstore_id);
    }

    public function cleanAssistantId($assistant_id)
    {
        return !empty($assistant_id) ? sanitize_text_field($assistant_id) : '';
    }

    public function cleanMultilingualSupport($multilingual_support)
    {
        return isset($multilingual_support) ? (bool) $multilingual_support : false;
    }

    public function cleanResponseLength($response_length)
    {
        if (!is_numeric($response_length)) {
            $this->errors[] = __('Openai Response Length must be a valid number.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        if ($response_length <= 0) {
            $this->errors[] = __('Openai Response Length must be a positive number greater than zero.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        return intval($response_length);
    }

    public function dataErrors()
    {
        return $this->errors;
    }
}