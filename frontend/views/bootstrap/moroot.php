<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

class MoRoot extends \BuddyBot\Frontend\Views\Moroot
{
    protected $sql;
    protected $atts;
    
    public function shortcodeHtml($atts, $content = null)
    {
        $html = '<div class="alert alert-warning" role="alert">';
        $html .= __('No HTML found for this view.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';
        return $html;
    }
}