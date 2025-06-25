<?php

namespace BuddyBot\Admin\Sql;

class EditChatBot extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('chatbot');
    }

    private function hasAdditionalInstructionsColumn()
    {
        global $wpdb;
        $column = $wpdb->get_results("SHOW COLUMNS FROM {$this->table} LIKE 'additional_instructions'");
        return !empty($column);
    }

    public function createBuddyBot($buddybot_data)
    {
        if (!$this->hasAdditionalInstructionsColumn()) {
            unset($buddybot_data['additional_instructions']);
        }

        $buddybot_data['created_on'] = current_time('mysql', 1);
        $buddybot_data['edited_on'] = current_time('mysql', 1);
        global $wpdb;
        $insert = $wpdb->insert(
            $this->table,
            $buddybot_data,
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
        if (!$this->hasAdditionalInstructionsColumn()) {
            unset($buddybot_data['additional_instructions']);
        }

        $where = array('id'=> $buddybot_data['id']);
        unset($buddybot_data['id']);

        $buddybot_data['edited_on'] = current_time('mysql', 1);
        global $wpdb;
        $update = $wpdb->update(
            $this->table,
            $buddybot_data,
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

    public function getCreatedOnById($id)
    {
        global $wpdb;
        $id = intval($id);

        $query = $wpdb->prepare(
            "SELECT created_on FROM {$this->table} WHERE id = %d LIMIT 1",
            $id
        );

        return $wpdb->get_var($query);
    }
}