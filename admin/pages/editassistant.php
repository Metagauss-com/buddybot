<div class="p-5">

<?php
$mo_assistant_page = new \MetagaussOpenAI\Admin\Html\Views\EditAssistant();
$mo_assistant_page->getHtml();

$mo_assistant_requests = new \MetagaussOpenAI\Admin\Requests\EditAssistant();
add_action('admin_footer', array($mo_assistant_requests, 'requestsJs'));
?>

</div>