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

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new MoConfig();
        }
        
        return self::$instance;
    }

}