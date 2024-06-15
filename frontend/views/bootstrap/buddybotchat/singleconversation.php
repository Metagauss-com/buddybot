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
        $html .= '<button id="buddybot-single-conversation-back-btn" type="button" class="btn btn-outline-dark rounded-4 p-3 d-flex">';
        $html .= '<span class="material-symbols-outlined">arrow_back_ios</span>';
        $html .= __('All Conversations', 'buddybot');
        $html .= '</button>';
        $html .= '</div>';
        return $html;
    }

    private function messagesBox()
    {
        $html = '<div id="buddybot-single-conversation-messages-wrapper" class="border p-3 mb-3">';
        $html .= '</div>';
        return $html;
    }

    private function newMessageInput()
    {
        $html = '<div id="buddybot-single-conversation-new-messages-wrapper" class="">';
        
        $html .= '<div class="">';
        $html .= '<textarea class="form-control rounded-4 p-3 border-dark" rows="3">';
        $html .= '</textarea>';
        $html .= '</div>';

        $html .= '<div class="text-center mt-3">';
        $html .= '<button type="button" class="btn btn-dark btn-lg p-3 rounded-5">';
        $html .= __('Ask A Question', 'buddybot');
        $html .= '</button>';
        $html .= '</textarea>';
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }
}