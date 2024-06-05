<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SingleConversation
{
    protected function singleConversationHtml()
    {
        $html = '<div id="buddybot-single-conversation-wrapper" class="container-fluid">';
        $html .= $this->backBtn();
        $html .= $this->messagesBox();
        $html .= $this->newMessageInput();
        $html .= '</div>';
        return $html;
    }

    protected function backBtn()
    {
        $html = '<div id="buddybot-single-conversation-back-btn-wrapper" class="mb-3">';
        $html .= '<button type="button" class="btn btn-light btn-sm d-flex">';
        $html .= '<span class="material-symbols-outlined">arrow_back_ios</span>';
        $html .= __('All Conversations', 'buddybot');
        $html .= '</button>';
        $html .= '</div>';
        return $html;
    }

    private function messagesBox()
    {
        $html = '<div id="buddybot-single-conversation-messages-wrapper">';
        $html .= '</div>';
        return $html;
    }

    private function newMessageInput()
    {
        $html = '<div id="buddybot-single-conversation-new-messages-wrapper" class="d-flex align-items-center">';
        
        $html .= '<div class="w-75">';
        $html .= '<textarea class="form-control">';
        $html .= '</textarea>';
        $html .= '</div>';

        $html .= '<div class="w-25">';
        $html .= '<button type="button" class="btn btn-primary btn-sm ms-2">Primary</button>';
        $html .= '</textarea>';
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }
}