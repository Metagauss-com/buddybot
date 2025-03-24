<?php
namespace BuddyBot\Frontend;

final class Sessions extends \BuddyBot\Frontend\MoRoot
{

    public function handleUserLogin($user_login, $user)
    {
        if (isset($_COOKIE['buddybot_session_id'])) {
            $session_id = $_COOKIE['buddybot_session_id'];
            $user_id = $user->ID;

            $sql = new \BuddyBot\Frontend\Sql\BuddybotChat($data='');
            $sql->convertSessionToUser($user_id, $session_id);
    
            setcookie("buddybot_session_id", "", time() - 3600, "/");
            unset($_COOKIE['buddybot_session_id']);
        }
    }
    
    public function __construct()
    {
        $this->setAll();
        add_action('wp_login', array($this, 'handleUserLogin'), 10, 2);
    }
}