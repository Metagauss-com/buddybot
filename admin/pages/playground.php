<div class="p-5">

<?php
$mo_playground_page = new \MetagaussOpenAI\Admin\Html\Views\Playground();
$mo_playground_page->getHtml();
add_action('admin_footer', array($mo_playground_page, 'pageJs'));

$mo_playground_requests = new \MetagaussOpenAI\Admin\Requests\Playground();
add_action('admin_footer', array($mo_playground_requests, 'requestsJs'));
?>

</div>