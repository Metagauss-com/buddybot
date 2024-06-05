<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SecurityChecks
{
    protected $errors = 0;

    protected function securityChecksHtml()
    {
        $html = $this->isUserLoggedIn();
        $html .= $this->isOpenAiKeySet();
        return $html;
    }

    protected function isUserLoggedIn()
    {
        $check = is_user_logged_in();

        if (!$check) {
            $this->errors += 1;
            return $this->userNotLoggedIn();
        }
    }

    private function userNotLoggedIn()
    {
        $html = '<div class="alert alert-danger small" role="alert">';
        $html .= __('You must be logged in to use this feature.', 'buddybot');
        $html .= '</div>';
        return $html;
    }

    protected function isOpenAiKeySet()
    {
        $openai_api_key = $this->sql->getOption('openai_api_key', '');
        $html = $openai_api_key;
        return $html;
    }
}