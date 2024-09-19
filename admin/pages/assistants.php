<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_assistants_page = new \BuddyBot\Admin\Html\Views\Assistants();
$mo_assistants_page->getHtml();
$mo_assistants_page->pageJs();
?>

</div>