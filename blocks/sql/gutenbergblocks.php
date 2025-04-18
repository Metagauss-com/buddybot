<?php

namespace BuddyBot\Blocks\Sql;

class  GutenbergBlocks extends \BuddyBot\Blocks\MoRoot
{
    public $config;
    protected $table;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('chatbot');
    }

    public function getAllBuddybots() {
        global $wpdb;
    
        // Construct the query to fetch all BuddyBots
        $query = "SELECT * FROM {$this->table}";
    
        // Execute the query and get the results
        $buddybots = $wpdb->get_results($query, ARRAY_A); // Fetch as an associative array
    
        return $buddybots;
    }
}