<?php
namespace BuddyBot;

use BuddyBot\Traits\Singleton;
final class bbOptions
{
    use Singleton;

    protected $config;
    protected $table;

    protected function setConfig()
    {
        $this->config = \BuddyBot\MoConfig::getInstance();
    }

    protected function setTable()
    {
        $this->table = $this->config->getDbTable('settings');
    }

    public function getOption(string $name, string $fallback = '', int $cache_expiry = 3600)
    {
        global $wpdb;
        
        $name = sanitize_text_field($name);
        $option_value = wp_cache_get($name, 'buddybot');

        static $table_checked = false;
        static $table_exists = false;

        if (!$table_checked) {
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->table));
            $table_checked = true;
        }

        if (!$table_exists) {
            return $fallback;
        }
    
        if ($option_value === false) {
            $option_value = $wpdb->get_var(
                $wpdb->prepare(
                    'SELECT option_value FROM %i WHERE option_name = %s',
                    esc_sql($this->table), $name
                )
            );
    
            if ($option_value === null) {
                return $fallback;
            }
    
            wp_cache_set($name, $option_value, 'buddybot', $cache_expiry);
        }
    
        // Decrypt the option value if needed
        $option_value = $this->decryptKey($name, $option_value);
    
        return $option_value;
    }
    

    private function decryptKey($name, $option_value)
    {
        $method = 'decrypt' . str_replace('_', '', $name);

        if (method_exists($this, $method)) {
            return $this->$method($option_value);
        } else {
            return $option_value;
        }
    }

    protected function decryptOpenAiApiKey($option_value)
    {
        $cipher = 'aes-128-cbc';
        $config = MoConfig::getInstance();
        $key = $config->getProp('c_key');

        return openssl_decrypt(
            $option_value,
            $cipher,
            $key,
            0,
            '6176693754375346'
        );
    }
}