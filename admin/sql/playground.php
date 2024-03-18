<?php

namespace MetagaussOpenAI\Admin\Sql;

class Playground extends \MetagaussOpenAI\Admin\Sql\MoRoot
{
    public function getThreadsByUserId($user_id = 0)
    {
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
}