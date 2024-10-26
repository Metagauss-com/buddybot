<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$default_buddybot_wizard_page = new \BuddyBot\Admin\Html\Views\DefaultBuddyBotWizard();
$default_buddybot_wizard_page->getHtml();

?>

</div>