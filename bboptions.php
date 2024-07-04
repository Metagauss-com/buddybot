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

    public function getOption(string $name, string $fallback = '')
    {
        global $wpdb;
        $option_value =  $wpdb->get_var(
            $wpdb->prepare(
                'SELECT option_value FROM %i WHERE option_name = %s',
                $this->table, $name
            )
        );

        if ($option_value === null) {
            return $fallback;
        }

        return $option_value;
    }
}