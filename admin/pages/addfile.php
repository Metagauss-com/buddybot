<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_addfile_page = new \BuddyBot\Admin\Html\Views\AddFile();
$mo_addfile_page->getHtml();
add_action('admin_print_footer_scripts', array($mo_addfile_page, 'pageJs'));

$mo_addfile_requests = new \BuddyBot\Admin\Requests\AddFile();
add_action('admin_print_footer_scripts', array($mo_addfile_requests, 'requestsJs'));
?>

</div>