<?php
namespace BuddyBot\Frontend\Sql;

use \BuddyBot\Traits\Singleton;

class MoRoot extends \BuddyBot\Frontend\Moroot
{
    use singleton;

    protected function isOptionSet($name, int $cache_expiry = 3600)
    {
        global $wpdb;    
        $table = esc_sql($this->config->getDbTable('settings'));    
        $cache_key = 'option_set_' . $name;
    
        $is_set = wp_cache_get($cache_key, 'buddybot');
    
        if ($is_set === false) {
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
    

    public function getOption($option_name, $default_value = '')
    {
        if ($this->isOptionSet($option_name)) {
            global $wpdb;
            return $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT option_value FROM %i WHERE option_name = %s LIMIT 1',
                    $this->config->getDbTable('settings'),
                    $option_name
                )
            );
        } else {
            return $default_value;
        }
    }

    protected function initializeSessionData()
    {
        global $wpdb;
        $table_name = $this->config->getDbTable('threads');
        $disable_cookies = $this->getOption('disable_cookies', 0);

        if ($disable_cookies) {
            return null;
        }

        $session_lifetime = $this->getOption('session_expiry', 24) * 3600;

        if (isset($_COOKIE['buddybot_session_data'])) {
            $cookie_data = json_decode(stripslashes($_COOKIE['buddybot_session_data']), true);

            if (isset($cookie_data['session_id']) && !empty($cookie_data['session_id'])) {
                return $cookie_data;
            }
        }

        do {
            $session_id = bin2hex(random_bytes(16));
            $sessionExists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE session_id = %s", $session_id));
        } while ($sessionExists > 0);

        $cookie_data = [
            'session_id' => $session_id,
        ];

        setcookie("buddybot_session_data", json_encode($cookie_data), time() + $session_lifetime, "/");

        return $cookie_data;
    }
}