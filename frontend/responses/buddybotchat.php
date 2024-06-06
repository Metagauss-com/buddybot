<?php
namespace BuddyBot\Frontend\Responses;

class BuddybotChat extends \BuddyBot\Frontend\Responses\Moroot
{
    public function getConversationList()
    {
        $buddybot_chat = \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat::getInstance();
        $buddybot_chat->conversationList();
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getConversationList', array($this, 'getConversationList'));
    }
}