<?php

namespace BuddyBot;

class Loader
{   
    public static function loadClass($class)
    {
        $is_buddybot_class = strpos($class, 'BuddyBot');

        if ($is_buddybot_class === 0) {
            $internal_class = str_replace('BuddyBot\\', '',buddybot-ai-custom-ai-assistant-and-chat-agent $class);
            $file = plugin_dir_path(__FILE__) . strtolower(str_replace('\\', '/', $internal_class)) . '.php';
            include $file;
        }
    }
}