<?php
namespace BuddyBot\Frontend\Sql;

class BuddybotChat extends \BuddyBot\Frontend\Sql\Moroot
{
    public function getConversationsByUserId($user_id)
    {
        global $wpdb;
        $conversations = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE user_id=%d',
                $this->config->getDbTable('threads'), $user_id
            )
        );

        return $conversations;
    }
}