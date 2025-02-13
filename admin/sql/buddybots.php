<?php

namespace BuddyBot\Admin\Sql;

class BuddyBots extends \BuddyBot\Admin\Sql\MoRoot
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

    public function getAllChatbots($offset = 0, $limit = 10, $orderby = 'created_on', $order = 'desc', $filter = [], $search = '')
    {
        global $wpdb;

        $valid_columns = ['chatbot_name', 'created_on', 'edited_on'];
        if (!in_array($orderby, $valid_columns)) {
            $orderby = 'created_on';
        }
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $where_clauses = ['1=1']; // Ensures there is always a valid WHERE clause
        $params = [];

        // Search by chatbot name
        if (!empty($search)) {
            $where_clauses[] = "chatbot_name LIKE %s";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        // Apply filters
        if (!empty($filter)) {
            $where_clauses[] = "chatbot_name = %s";
            $params[] = $filter;
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

        // Construct query
        $query = "SELECT * FROM {$this->table} $where_sql ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";

        // Add a dummy placeholder
        return $wpdb->get_results($wpdb->prepare($query, ...array_merge($params, [$limit, $offset])), ARRAY_A);
    }


    public function getTotalChatbotsCount($search = '', $filter = '')
    {
        global $wpdb;

        $where_clauses = ['1=1'];
        $params = [];

        if (!empty($search)) {
            $where_clauses[] = "chatbot_name LIKE %s";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        if (!empty($filter)) {
            $where_clauses[] = "assistant_id = %s";
            $params[] = $filter;
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        $query = "SELECT COUNT(*) FROM {$this->table} $where_sql";

        return empty($params) ? (int) $wpdb->get_var($query) : (int) $wpdb->get_var($wpdb->prepare($query, ...$params));
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