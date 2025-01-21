<?php

namespace BuddyBot\Admin\Sql;

class ViewConversation extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('threads');
    }

    public function getTotalConversationsCountExcludingThread($thread_id, $user_id = 0)
    {
        global $wpdb;

        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE user_id = %d AND thread_id != %s';

        // Prepare the final query with user_id and exclude_thread_id
        $query = $wpdb->prepare($query, $user_id, $thread_id);
        $count = $wpdb->get_var($query);

        return $count;
    }
}