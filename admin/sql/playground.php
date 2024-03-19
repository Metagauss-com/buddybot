<?php

namespace MetagaussOpenAI\Admin\Sql;

class Playground extends \MetagaussOpenAI\Admin\Sql\MoRoot
{
    public function getThreadsByUserId($user_id = 0)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $user_id = absint($user_id);
        $table = $this->config->getDbTable('threads');

        if ($user_id < 1) {
            $user_id = get_current_user_id();
        }

        global $wpdb;
        $this->response['result'] = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE user_id=%d',
                $table,
                $user_id
            )
        );

        return $this->returnResponse();
    }

    public function saveThreadId($thread_id)
    {
        if (!is_user_logged_in()) {
            return;
        }

        $table = $this->config->getDbTable('threads');
        
        $data = array(
            'thread_id' => $thread_id,
            'user_id' => get_current_user_id()
        );

        $format = array('%s', '%d');

        global $wpdb;
        $insert = $wpdb->insert($table, $data, $format);
        return $insert;


    }
}