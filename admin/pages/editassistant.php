<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_assistant_page = new \BuddyBot\Admin\Html\Views\EditAssistant();
$mo_assistant_page->getHtml();
$mo_assistant_page->pageJs();
?>

</div>