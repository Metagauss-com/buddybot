<?php

namespace BuddyBot\Admin\Sql;

class EditBuddyBot extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('chatbot');
    }

    public function createBuddyBot($buddybot_data)
    {
        $data = $buddybot_data;
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

    public function updateBuddyBot($buddybot_data)
    {
        $where = array('id'=> $buddybot_data['id']);
        $data = $buddybot_data;
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

    public function getBuddyBotById($id)
    {
        $id = intval($id);
        global $wpdb;
        $buddybot = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE id = %d LIMIT 1',
                $this->table,
                $id
            )
        );

        if (empty($buddybot)) {
            return false;
        } else {
            return $buddybot[0];
        }
    }
}