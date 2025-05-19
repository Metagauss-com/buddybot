<?php
namespace BuddyBot\Includes;

final class ChatBubble extends \BuddyBot\Admin\MoRoot
{

    public function getHtml()
    {
        $this->chatBuubleHeader();
    }

    private function chatBuubleHeader()
    {
        echo '<div class="bb-chatbubble-header">';
        echo '<h1 class="wp-heading-inline">';
        echo esc_html(__('Chat Bubble', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</h1>';
    }

}