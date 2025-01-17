<?php

namespace BuddyBot\Admin\Sql;

class Chatbot extends \BuddyBot\Admin\Sql\MoRoot
{
    public $config;
    
    protected function setTable()
    {
        $this->table = $this->config->getDbTable('logs');
    }

    // Add a new log entry
    public function addLogEntry($event, $status, $description = '', $botId = null, $details = '{}', $ipAddress = '', $severity = 'INFO', $component = '', $referrerUrl = '') {
        $event = sanitize_text_field($event); 
        $status = sanitize_text_field($status); 
        $description = sanitize_textarea_field($description); 
        $botId = is_numeric($botId) ? absint($botId) : null;
        $details = is_array($details) ? wp_json_encode($details) : $details;
        $ipAddress = sanitize_text_field($ipAddress);
        $severity = sanitize_text_field($severity);
        $component = sanitize_text_field($component);
        $referrerUrl = esc_url_raw($referrerUrl);

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
            $this->table,
            $data,
            array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($insert !== false) {
            return $wpdb->insert_id;
        } else {
            return false;
        }

    }

    // Delete a log entry by ID
    public function deleteLogById($id) {
        $id = (int)$id;

        global $wpdb;
        $result = $wpdb->delete(
            $this->table,
            ['log_id' => $id],
            ['%d']
        );

        if ($result === false) {
            return false;
        }

        return $result;
    }

    // Delete log entries by component and severity
    public function deleteLogsByComponentAndSeverity($component = null, $severity = null) {
        $conditions = [];
        $placeholders = [];

        if ($component !== null) {
            $component = sanitize_text_field($component);
            $conditions[] = 'log_component = %s';
            $placeholders[] = $component;
        }

        if ($severity !== null) {
            $severity = sanitize_text_field($severity);
            $conditions[] = 'log_severity = %s';
            $placeholders[] = $severity;
        }

        if (empty($conditions)) {
            return false;
        }

        global $wpdb;

        $query = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $conditions);
        $result = $wpdb->query($wpdb->prepare($query, ...$placeholders));

        if ($result === false) {
            return false;
        }

        return $result;
    }

    // Delete logs older than a specific timestamp
    public function deleteOlderThan($timestamp = null) {
        if ($timestamp === null) {
            $timestamp = date('Y-m-d H:i:s', strtotime('-30 days'));
        }

        global $wpdb;
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->table} WHERE log_timestamp < %s",
                $timestamp
            )
        );

        if ($result === false) {
            return false;
        }

        return $result;
    }

    // Delete logs older than the most recent specified number
    public function deleteOlderThanRecent($number = 100) {
        $number = (int)$number;

        global $wpdb;
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->table} WHERE log_id NOT IN (
                    SELECT log_id FROM (
                        SELECT log_id FROM {$this->table} ORDER BY log_timestamp DESC LIMIT %d
                    ) as temp_table
                )",
                $number
            )
        );

        if ($result === false) {
            return false;
        }

        return $result;
    }

    public function getByComponent($component, $number = 10) {

        $number = (int)$number;
        $component = sanitize_text_field($component);

        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE log_component = %s ORDER BY log_timestamp DESC LIMIT %d",
                $component,
                $number
            ),
            ARRAY_A
        );
        if (empty($results)) {
            return false;
        }

        return $results;
    }

    public function getBySeverity($severity, $number = 10) {

        $number = (int)$number;
        $severity = sanitize_text_field($severity);

        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE log_severity = %s ORDER BY log_timestamp DESC LIMIT %d",
                $severity,
                $number
            ),
            ARRAY_A
        );
        if (empty($results)) {
            return false;
        }

        return $results;
    }
}