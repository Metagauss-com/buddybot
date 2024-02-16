<div class="bg-light p-5">

<?php
$mo_chatbot_page = new \MetagaussOpenAI\Admin\Html\Views\ChatBot();
$mo_chatbot_page->getHtml();

$mo_chatbot_requests = new \MetagaussOpenAI\Admin\Requests\ChatBot();
add_action('admin_footer', array($mo_chatbot_requests, 'requestsJs'));
?>

</div>