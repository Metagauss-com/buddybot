<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_files_page = new \BuddyBot\Admin\Html\Views\OrgFiles();
$mo_files_page->getHtml();
?>

</div>