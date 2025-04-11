<div class="px-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_chatbot_page = new \BuddyBot\Admin\Html\Views\ViewConversation();
$mo_chatbot_page->getHtml();
$mo_chatbot_page->pageJs();
?>

</div>