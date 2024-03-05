<?php

namespace MetagaussOpenAI;

final class MoConfig
{
    protected static $instance;

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

}