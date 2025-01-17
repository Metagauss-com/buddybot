<?php

namespace BuddyBot;

final class MoConfig
{
    public const PREFIX = "buddybot";
    protected static $instance;
    protected $db_tables;
    protected $c_key = 'nsalfpn8ec';
    protected $unsupported_models = array();
    protected $date_format;
    protected $time_format;

    private function setDbTables()
    {
        global $wpdb;
        $prefix = $wpdb->prefix . 'buddybot_';
        $this->db_tables = array(
            'threads' => $prefix . 'threads',
            'chatbot' => $prefix . 'chatbot',
            'settings' => $prefix . 'settings',
            'logs' => $prefix . 'logs'
        );
    }

    public function getDbTable($for = '')
    {
        if (array_key_exists($for, $this->db_tables)) {
            return $this->db_tables[$for];
        } else {
            return false;
        }
    }

    protected function setUnsupportedModels()
    {
        $this->unsupported_models = array(
            'dall-e-2',
            'text-embedding-3-large',
            'whisper-1',
            'tts-1-hd-1106',
            'tts-1-hd',
            'gpt-3.5-turbo-0301',
            'dall-e-3',
            'gpt-3.5-turbo-instruct-0914',
            'tts-1',
            'davinci-002',
            'gpt-3.5-turbo-instruct',
            'babbage-002',
            'gpt-4-vision-preview',
            'tts-1-1106',
            'text-embedding-ada-002',
            'text-embedding-3-small',
            'o1-mini-2024-09-12',
            'o1-preview',
            'o1-preview-2024-09-12',
            'o1-mini',
            'gpt-4o-realtime-preview',
            'gpt-4o-realtime-preview-2024-10-01',
            'gpt-4o-audio-preview',
            'gpt-4o-audio-preview-2024-10-01',
            'chatgpt-4o-latest',
            'gpt-3.5-turbo-16k',
            'omni-moderation-2024-09-26',
            'omni-moderation-latest',
            'gpt-4',
            'gpt-4-0613',
            'gpt-4o-realtime-preview-2024-12-17',
            'gpt-4o-mini-realtime-preview',
            'gpt-4o-mini-realtime-preview-2024-12-17',
            'gpt-4o-audio-preview-2024-12-17',
            'gpt-4o-mini-audio-preview',
            'gpt-4o-mini-audio-preview-2024-12-17',
        );
    }

    protected function setDateFormat()
    {
        $this->date_format = get_option('date_format');
    }

    protected function setTimeFormat()
    {
        $this->time_format = get_option('time_format');
    }

    public function getProp(string $prop_name)
    {
        if (property_exists($this, $prop_name)) {
            return $this->$prop_name;
        } else {
            return false;
        }
    }

    public function getRootUrl() {
        return  plugin_dir_url(__FILE__);
    }    
    
    public function getRootPath() {
        return  plugin_dir_path(__FILE__);
    }

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new MoConfig();
        }
        
        return self::$instance;
    }

    private function __construct()
    {
        $this->setDateFormat();
        $this->setTimeFormat();
        $this->setDbTables();
        $this->setUnsupportedModels();
    }

}