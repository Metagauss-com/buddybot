<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

class BuddybotChat extends \BuddyBot\Frontend\Views\Bootstrap\MoRoot
{
    public function shortcodeHtml($atts, $content = null)
    {
        $html = '<div id="buddybot-chat-wrapper">';
        $html .= $this->backBtn();
        $html .= $this-conversationBlock();
        $html .= '</div>';
        return $html;
    }

    public function backBtn()
    {
        $html = '<div id="buddybot-chat-back-btn-wrapper">';
        $html .= '<button type="button" class="btn btn-light d-flex">';
        $html .= '<span class="material-symbols-outlined">arrow_back_ios</span>';
        $html .= __('All Conversations', 'buddybot');
        $html .= '</button>';
        $html .= '</div>';
        return $html;
    }
}