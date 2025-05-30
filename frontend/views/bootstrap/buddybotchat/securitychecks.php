<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SecurityChecks
{
    protected $errors = 0;

    protected function securityChecksHtml()
    {
        $html  = $this->isUserLoggedIn();
        $html .= $this->isOpenAiKeySet();
        $html .= $this->chatbotExists();
        return $html;
    }

    protected function isUserLoggedIn()
    {
       $visitorchat = $this->sql->getOption('enable_visitor_chat', 0);

       if ($visitorchat == "1") {
            return;
        }

        $check = is_user_logged_in();

        if (!$check) {
            $this->errors += 1;
            $html = $this->userNotLoggedIn();
            return $html;
        }
    }

    private function userNotLoggedIn()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('You must be logged in to use this feature.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';
        return $html;
    }

    protected function isOpenAiKeySet()
    {
        $openai_api_key = $this->sql->getOption('openai_api_key', '');
        
        if (empty($openai_api_key)) {
            $this->errors += 1;
            $html = $this->openAiApiKeyNotSet();
            return $html;
        }
    }

    private function openAiApiKeyNotSet()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('API Key Missing.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';
        return $html;
    }

    private function chatbotExists()
    {
        //print_r($this->atts);die;
        if (!isset($this->atts['id']) || empty($this->atts['id'])) {
            $this->errors++;
            $html = $this->missingChatbotId();
            return $html;
        }

        $chatbot = $this->sql->getChatbot($this->atts['id']);

        if ($chatbot === null) {
            $this->errors += 1;
            $html = $this->invalidChatbot();
            return $html;
        } else {
            $this->chatbot = $chatbot;
        }
    }

    private function invalidChatbot()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('Invalid Chatbot ID. Unable to proceed.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';
        return $html;
    }

    private function missingChatbotId()
    {
        return '<div class="alert alert-danger small" role="alert">'
            . __('Missing Chatbot ID.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
            . '</div>';
    }
}