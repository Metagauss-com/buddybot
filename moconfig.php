<?php

namespace MetagaussOpenAI;

final class MoConfig
{
    protected static $instance;
    protected $unsupported_models = array();

    const PREFIX = "MetagaussOpenAI";
    
    public function isCurlSet()
    {
        if  (in_array  ('curl', get_loaded_extensions())) {
            return true;
        }
        else {
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
            'text-embedding-3-small'
        );
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
        $this->setUnsupportedModels();
    }

}