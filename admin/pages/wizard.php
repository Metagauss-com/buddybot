<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_wizard_page = new \BuddyBot\Admin\Html\Views\Wizard();
$mo_wizard_page->getHtml();
$mo_wizard_page->pageJs();
?>

</div>