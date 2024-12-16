<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$key = \BuddyBot\bbOptions::getInstance()->getOption('openai_api_key', '');
$buddybot_checks_data = '';

if(!empty($key)){
$buddybot_checks_data = ['custom_checks' => ['capability']];
}
$buddybot_checks = new BuddyBot\Admin\InitialChecks($buddybot_checks_data);

if ($buddybot_checks->hasErrors()) {
    return;
}

$mo_assistant_page = new \BuddyBot\Admin\Html\Views\VectorStore();
$mo_assistant_page->getHtml();
$mo_assistant_page->pageJs();
?>

</div>