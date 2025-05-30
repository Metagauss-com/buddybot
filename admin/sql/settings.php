<?php

namespace BuddyBot\Admin\Sql;

class Settings extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;

    protected function setTable()
    {
        $this->table = $this->config->getDbTable('settings');
    }

    protected function isOptionSet($name, int $cache_expiry = 3600)
    {
        global $wpdb;    
        $table = esc_sql($this->table);
    
        $cache_key = 'option_set_' . $name;    
        $is_set = wp_cache_get($cache_key, 'buddybot');
    
        if ($is_set === false) {
            // If the cache does not have the value, query the database
            $is_set = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT EXISTS(SELECT 1 FROM %i WHERE option_name = %s LIMIT 1)",
                    $table, $name
                )
            );
    
            wp_cache_set($cache_key, $is_set, 'buddybot', $cache_expiry);
        }
    
        return $is_set;
    }
    

    public function saveOption($name, $value)
    {

        $value = $this->encryptKey($name, $value);

        global $wpdb;

        if ($this->isOptionSet($name)) {
            $wpdb->update(
                $this->table,
                array(
                    'option_value' => maybe_serialize($value),
                    'last_editor' => get_current_user_id(),
                    'edited_on' => current_time('mysql', true)
                ),
                array('option_name' => $name),
                array('%s', '%d', '%s'),
                array('%s')
            );
        } else {
            $wpdb->insert(
                $this->table,
                array(
                    'option_name' => $name,
                    'option_value' => maybe_serialize($value),
                    'last_editor' => get_current_user_id(),
                    'edited_on' => current_time('mysql', true)
                ),
                array('%s', '%s', '%d', '%s')
            );
        }
    }

    private function encryptKey($name, $value)
    {
        $method = 'encrypt' . str_replace('_', '', $name);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        } else {
            return $value;
        }
    }

    private function encryptOpenAiApiKey($option_value)
    {
        $cipher = 'aes-128-cbc';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $key = $this->config->getProp('c_key');

        return openssl_encrypt(
            $option_value,
            $cipher,
            $key,
            0,
            '6176693754375346'
        );
    }

    public function getExpiredThreads($expiry_days)
    {
        global $wpdb;
        $table = $this->config->getDbTable('threads');

        return $wpdb->get_col(
            $wpdb->prepare(
                "SELECT thread_id FROM $table 
                 WHERE user_type = 'visitor' 
                 AND created < (UTC_TIMESTAMP() - INTERVAL %d DAY)",
                $expiry_days
            )
        );
    }

    public function deleteExpiredThread($thread_id)
    {
        $table = $this->config->getDbTable('threads');
        $where = array('thread_id' => $thread_id);
        $format = array('%s');

        global $wpdb;
        return $wpdb->delete($table, $where, $format);
    }

    public function addLogEntry($event, $status, $description = '', $botId = null, $details = '{}', $ipAddress = '', $severity = 'INFO', $component = '', $referrerUrl = '') {
        $table = $this->config->getDbTable('logs');

        $data = [
            'log_event' => $event,
            'log_status' => $status,
            'log_description' => $description,
            'log_bot_id' => $botId,
            'log_details' => $details,
            'log_ip_address' => $ipAddress,
            'log_severity' => $severity,
            'log_component' => $component,
            'log_referrer_url' => $referrerUrl,
            'log_timestamp' => current_time('mysql', 1)
        ];

        global $wpdb;
        $insert = $wpdb->insert(
            $table,
            $data,
            array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($insert !== false) {
            return $wpdb->insert_id;
        } else {
            return false;
        }

    }
}