<?php

namespace MetagaussOpenAI;

class Loader
{   
    public static function loadClass($class)
    {
        $file = (plugin_dir_path(__DIR__) . strtolower(str_replace('\\', '/', $class)) . '.php');
        echo $file;
        $is_internal = strpos($file, 'MetagaussOpenAI');

        if ($is_internal !== false) {
            include($file);
        }
    }
}