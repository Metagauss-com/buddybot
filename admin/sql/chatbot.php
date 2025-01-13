<?php

namespace BuddyBot\Admin\Sql;

class Chatbot extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('chatbot');
    }

    public function getFirstChatbotId()
    {
        global $wpdb;
        $chatbot = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT id FROM %i ORDER BY id ASC LIMIT 1',
                $this->table
            )
        );
        if(empty($chatbot)) {
            return false;
        } else {
            return $chatbot[0]->id;
        }
    }

    public function createChatbot($chatbot_data)
    {
        $data = $chatbot_data;
        global $wpdb;
        $insert = $wpdb->insert(
            $this->table,
            $data,
            array('%s', '%s', '%s')
        );

        if ($insert !== false) {
            return $wpdb->insert_id;
        } else {
            return false;
        }

    }

    public function updateChatbot($chatbot_data)
    {
        $where = array('id'=> $chatbot_data['id']);
        $data = $chatbot_data;
        unset($data['id']);
        
        global $wpdb;
        $update = $wpdb->update(
            $this->table,
            $data,
            $where,
            array('%s', '%s', '%s'),
            array('%d')
        );

        return $update;

    }

    public function removeAssistantId($chatbot_id)
    {
        $where = array('id' => $chatbot_id);
        $data = array('assistant_id' => NULL);

        global $wpdb;
        
        $update = $wpdb->update(
            $this->table, 
            $data,     
            $where,         
            array('%s'),   
            array('%d')   
        );
        
        return $update;
    }

    public function getAllChatbots($offset = 0, $limit = 10)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            'SELECT * FROM ' . $this->table . ' LIMIT %d OFFSET %d',
            $limit,
            $offset
        );

        // Execute the query and return the results as an associative array
        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    public function getTotalChatbotsCount()
    {
        global $wpdb;

        $query = $wpdb->prepare(
            'SELECT COUNT(*) FROM ' . $this->table
        );

        $count = $wpdb->get_var($query);

        return $count;
    }

    public function deleteChatbot($chatbot_id)
    {
        $where = array('id' => $chatbot_id);

        global $wpdb;
        $delete = $wpdb->delete(
            $this->table,
            $where,
            array('%d')
        );

        return $delete;
    }
}