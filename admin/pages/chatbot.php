<div class="p-5">

<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

$buddybot_checks = new BuddyBot\Admin\InitialChecks();

if ($buddybot_checks->hasErrors()) {
    return;
}
$buddybot_extension_active = apply_filters('buddybot_extension_active', false);
$mo_chatbot_page = new \BuddyBot\Admin\Html\Views\ChatBot();

if ($buddybot_extension_active) {
    $id = filter_input(INPUT_GET,'chatbot_id', FILTER_SANITIZE_NUMBER_INT);
    if($id==false || $id==null)
    {
        $create_mode = filter_input(INPUT_GET, 'create', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if ($create_mode === 'true') {
            $mo_chatbot_page->getHtml();
            $mo_chatbot_page->pageJs();
        } else {
            do_action('buddybot_menu_content');
        }
    } else {
        $mo_chatbot_page->getHtml();
        $mo_chatbot_page->pageJs();
    }   

} else {
    $mo_chatbot_page->getHtml();
    $mo_chatbot_page->pageJs();
}

?>

</div>