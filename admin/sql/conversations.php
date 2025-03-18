<?php

namespace BuddyBot\Admin\Sql;

class Conversations extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('threads');
    }

    public function getAllConversations($offset = 0, $limit = 10, $orderby = 'created', $order = 'desc', $user_id = 0, $search = '')
    {
        global $wpdb;

        $valid_columns = ['user_id', 'created', 'thread_name'];
        if (!in_array($orderby, $valid_columns)) {
            $orderby = 'created';
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $where_clauses = ['1=1'];
        $params = [];

        if (!empty($search)) {
            $where_clauses[] = "thread_name LIKE %s";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }

        if (!empty($user_id)) {
            $where_clauses[] = "user_id = %d";
            $params[] = $user_id;
        }

        $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);

        $query = "SELECT * FROM {$this->table} $where_sql ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($query, ...array_merge($params, [$limit, $offset])), ARRAY_A);
    }

    public function getTotalConversationsCount($user_id = 0, $search = '')
    {
        global $wpdb;

        $where_clauses = ['1=1'];
        $params = [];
    
        if (!empty($search)) {
            $where_clauses[] = "thread_name LIKE %s";
            $params[] = '%' . $wpdb->esc_like($search) . '%';
        }
    

        if (!empty($user_id)) {
            $where_clauses[] = "user_id = %d";
            $params[] = $user_id;
        }
    
        $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
        $query = "SELECT COUNT(*) FROM {$this->table} $where_sql";

        return empty($params) ? (int) $wpdb->get_var($query) : (int) $wpdb->get_var($wpdb->prepare($query, ...$params));
    }

    public function deleteConversation($thread_id)
    {
        $table = $this->config->getDbTable('threads');
        $where = array('thread_id' => $thread_id);
        $format = array('%s');

        global $wpdb;
        return $wpdb->delete($table, $where, $format);
    }

    public function getUserIds()
    {
        global $wpdb;
        return $wpdb->get_col("SELECT DISTINCT user_id FROM {$this->table} WHERE user_id IS NOT NULL");
    }
}