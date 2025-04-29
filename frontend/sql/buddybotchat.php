<?php
namespace BuddyBot\Frontend\Sql;

class BuddybotChat extends \BuddyBot\Frontend\Sql\Moroot
{
    public function getDefaultBuddybotId(int $cache_expiry = 3600)
    {
        global $wpdb;
    
        $table = esc_sql($this->config->getDbTable('chatbot'));
    
        $cache_key = 'default_buddybot_id';
    
        $id = wp_cache_get($cache_key, 'buddybot');
    
        // if ($id === false) {
        //     // If the cache does not have the value, query the database
        //     $id = $wpdb->get_var(
        //         $wpdb->prepare(
        //             'SELECT id FROM %i LIMIT 1', $table
        //         )
        //     );
    
        //     // Cache the result for future use with an expiry time
        //     wp_cache_set($cache_key, $id, 'buddybot', $cache_expiry);
        // }
    
        return $id;
    }
    

    public function getChatbot($chatbot_id, int $cache_expiry = 3600)
    {
        global $wpdb;
    
        $table = esc_sql($this->config->getDbTable('chatbot'));    
        $cache_key = 'chatbot_' . $chatbot_id;
    
        $chatbot = wp_cache_get($cache_key, 'buddybot');
    
        if ($chatbot === false) {
            $chatbot = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM %i WHERE id = %d",
                    $table, $chatbot_id
                )
            );
    
            wp_cache_set($cache_key, $chatbot, 'buddybot', $cache_expiry);
        }
    
        return $chatbot;
    }
    

    public function getConversationsByUserId($user_id, int $cache_expiry = 3600)
    {
        global $wpdb;
    
        // Sanitize the table name securely
        $table = esc_sql($this->config->getDbTable('threads'));
    
        // Define a cache key for this query
        $cache_key = 'conversations_user_' . $user_id;
    
        // Attempt to get the cached value
        $conversations = wp_cache_get($cache_key, 'buddybot');
    
        if ($conversations === false) {
            // If the cache does not have the value, query the database
            $conversations = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM %i WHERE user_id = %d",
                    $table, $user_id
                )
            );
    
            // Cache the result for future use with an expiry time
            wp_cache_set($cache_key, $conversations, 'buddybot', $cache_expiry);
        }
    
        return $conversations;
    }

    public function getConversationsBySessionId($session_id, int $cache_expiry = 3600)
    {
        global $wpdb;

        $table = esc_sql($this->config->getDbTable('threads'));

        $cache_key = 'conversations_session_' . $session_id;

        $conversations = wp_cache_get($cache_key, 'buddybot');

        if ($conversations === false) {

            $conversations = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $table WHERE session_id = %s",
                    $session_id
                )
            );

            wp_cache_set($cache_key, $conversations, 'buddybot', $cache_expiry);
        }

        return $conversations;
    }
    

    public function saveThreadInDb($thread_id)
    {
        $table = esc_sql($this->config->getDbTable('threads'));
        $cache_key = 'conversations_user_' . get_current_user_id();

        $user_id = get_current_user_id();
        $session_id = null;
        $visitor_id = null;
        $user_type = 'visitor';

        if ($user_id === 0) {
            $session_data = $this->initializeSessionData();
            
            if (is_array($session_data)) {
                $session_id = $session_data['session_id'] ?? null;
                $visitor_id = $session_data['visitor_id'] ?? null;
            }

            $user_details = [];

            if (!empty($visitor_id)) {
                $user_details['email'] = $visitor_id;
            }
        } else {
            $user_type = 'logged_in';
        }

        $data = array(
            'thread_id' => $thread_id,
            'user_id' => $user_id,
            'session_id'  => $session_id,
            'user_type'   => $user_type,
            'user'        => wp_json_encode($user_details),
            'created' => current_time('mysql', true)
        );

        global $wpdb;
        $insert = $wpdb->insert($table, $data, array('%s', '%d', '%s', '%s', '%s'));

        if ($insert) {
            wp_cache_delete($cache_key, 'buddybot');
        }

        return $insert;
    }

    public function updateThreadName($thread_id, $thread_name)
    {
        $thread_id = sanitize_text_field($thread_id);
        $thread_name = sanitize_text_field($thread_name);
    
        // Shorten thread name if it exceeds 100 characters
        if (strlen($thread_name) > 100) {
            $thread_name = substr($thread_name, 0, 100);
        }
    
        $table = $this->config->getDbTable('threads');
    
        global $wpdb;
    
        // Update the thread name in the database
        $wpdb->update(
            $table,
            array('thread_name' => $thread_name),
            array('thread_id' => $thread_id),
            array('%s'),
            array('%s')
        );
    }
    

    public function deleteThread($thread_id)
    {
        $table = $this->config->getDbTable('threads');
        $where = array('thread_id' => $thread_id);

        global $wpdb;
        return $wpdb->delete($table, $where, ['%s']);
    }

    public function isThreadOwner($thread_id, $user_id)
    {
        $table = $this->config->getDbTable('threads');

        global $wpdb;

        $thread_owner_id = $wpdb->get_var(
            $wpdb->prepare(
                'SELECT user_id FROM %i WHERE thread_id = %s', $table, $thread_id
            )
        );

        if ($user_id === absint($thread_owner_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function convertSessionToUser($user_id, $session_id)
    {
        global $wpdb;
        $table = esc_sql($this->config->getDbTable('threads'));

        return $wpdb->update(
            $table,
            array('user_id' => $user_id, 'session_id' => null, 'user_type' => 'logged_in'),
            array('session_id' => $session_id),
            array('%d', 'NULL', '%s'),
            array('%s')
        );
    }
}