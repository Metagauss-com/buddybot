<?php
namespace BuddyBot\Frontend\Requests;

class BuddybotChat extends \BuddyBot\Frontend\Requests\Moroot
{
    protected function shortcodeJs()
    {
        echo '
        getUserId();
        function getUserId() {

            const data = {
                "action": "getConversationList"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-chat-conversation-list-wrapper").html(response);
            });
        }
        ';
    }
}