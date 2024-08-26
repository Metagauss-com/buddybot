<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_chatbot_page = new \BuddyBot\Admin\Html\Views\ChatBot();
$mo_chatbot_page->getHtml();
add_action('admin_print_footer_scripts', array($mo_chatbot_page, 'pageJs'));

$mo_chatbot_requests = new \BuddyBot\Admin\Requests\ChatBot();
add_action('admin_print_footer_scripts', array($mo_chatbot_requests, 'requestsJs'));
?>

</div>