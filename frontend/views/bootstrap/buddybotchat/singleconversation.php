<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SingleConversation
{
    protected function singleConversationHtml()
    {
        $html = '<div id="buddybot-single-conversation-wrapper" class="container-fluid">';
        $html .= $this->conversationActions();
        $html .= $this->messagesBox();
        $html .= $this->statusBar();
        $html .= $this->newMessageInput();
        $html .= '</div>';
        return $html;
    }

    private function conversationActions()
    {
        $html = '<div class="d-flex justify-content-between align-items-center">';
        
        $html .= '<div class="d-flex">';

        $html .= '<div id="buddybot-single-conversation-back-btn" class="" role="button">';
        $html .= $this->mIcon('arrow_back_ios');
        $html .= '</div>';

        $html .= '</div>';
        
        
        $html .= '<div class="d-flex align-items-center">';
        
        $html .= '<div id="buddybot-single-conversation-load-messages-btn" class="mx-1" role="button">';
        $html .= $this->mIcon('cloud_download');
        $html .= '</div>';

        $html .= '<div class="mx-1" role="button">';
        $html .= $this->mIcon('delete');
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }

    private function statusBar()
    {
        $html = '<div id="buddybot-single-conversation-status-bar">';
        $html .= '</div>';
        return $html;
    }

    private function messagesBox()
    {
        $html = '<div id="buddybot-single-conversation-messages-wrapper" class="mb-4 small" style="max-height:400px;overflow:auto;">';
        $html .= '</div>';
        return $html;
    }

    private function newMessageInput()
    {
        $html = '<div id="buddybot-single-conversation-new-messages-wrapper" class="">';
        
        $html .= '<div class="">';
        $html .= '<textarea class="form-control rounded-4 p-3 border-bottom border-dark border-2 shadow-0" rows="3" ';
        $html .= 'placeholder="' . __('Type your question here.', 'buddybot') . '">';
        $html .= '</textarea>';
        $html .= '</div>';

        $html .= '<div class="text-center mt-3">';
        $html .= '<button type="button" class="btn btn-dark p-3 rounded-5">';
        $html .= __('Send', 'buddybot');
        $html .= '</button>';
        $html .= '</textarea>';
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }
}