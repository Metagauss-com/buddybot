<?php

namespace MetagaussOpenAI\Admin\Sql;

class Chatbot extends \MetagaussOpenAI\Admin\Sql\MoRoot
{
    public function saveChatbot($chatbot_data)
    {
        $table = $this->config->getDbTable('chatbot');
        $data = $chatbot_data;
        global $wpdb;
        $insert = $wpdb->insert(
            $table,
            $data,
            array('%s', '%s', '%s')
        );

        if ($insert !== false) {
            return $wpdb->insert_id;
        } else {
            return false;
        }

    }
}