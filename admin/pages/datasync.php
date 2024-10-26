<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_datasync_page = new \BuddyBot\Admin\Html\Views\DataSync();
$mo_datasync_page->getHtml();
$mo_datasync_page->pageJs();
?>

</div>