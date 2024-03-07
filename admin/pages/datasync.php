<div class="p-5">

<?php
$mo_datasync_page = new \MetagaussOpenAI\Admin\Html\Views\DataSync();
$mo_datasync_page->getHtml();
add_action('admin_footer', array($mo_datasync_page, 'pageJs'));

$mo_datasync_requests = new \MetagaussOpenAI\Admin\Requests\DataSync();
add_action('admin_footer', array($mo_datasync_requests, 'requestsJs'));
?>

</div>