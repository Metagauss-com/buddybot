<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait ConversationList
{
    protected $conversations;

    protected function setConversations()
    {
        $user_id = get_current_user_id();
        echo $user_id . '<br>';
        $this->conversations = $this->sql->getConversationsByUserId($user_id);
    }

    protected function conversationListHtml()
    {
        $html = var_dump($this->conversations);
        return $html;
    }
}