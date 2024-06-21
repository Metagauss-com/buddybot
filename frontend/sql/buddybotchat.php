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

    public function updateThreadName($thread_id, $thread_name)
    {
        $table = $this->config->getDbTable('threads');
        
        if (strlen($thread_name) > 100) {
            $thread_name = substr($thread_name, 100);
        }

        $data = array('thread_name' => $thread_name);
        $where = array('thread_id' => $thread_id);

        global $wpdb;
        $wpdb->update($table, $data, $where, array('%s'), array('%s'));
    }
}