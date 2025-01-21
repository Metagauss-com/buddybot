<?php

namespace BuddyBot\Admin\Sql;

class Conversations extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('threads');
    }

    public function getAllConversations($offset = 0, $limit = 10, $user_id = 0)
    {
        global $wpdb;

        $query = 'SELECT * FROM ' . $this->table;

        if ($user_id) {
            $query .= ' WHERE user_id = %d';
        }

        $query .= ' LIMIT %d OFFSET %d';

        if ($user_id) {
            $query = $wpdb->prepare($query, $user_id, $limit, $offset);
        } else {
            $query = $wpdb->prepare($query, $limit, $offset);
        }

        // Execute the query and return the results as an associative array
        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    public function getTotalConversationsCount($user_id = 0)
    {
        global $wpdb;
    
        $query = 'SELECT COUNT(*) FROM ' . $this->table;

        if ($user_id) {
            $query .= ' WHERE user_id = %d';
        }
    
        if ($user_id) {
            $query = $wpdb->prepare($query, $user_id);
        } else {
            $query = $wpdb->prepare($query);
        }

        $count = $wpdb->get_var($query);
    
        return $count;
    }
}