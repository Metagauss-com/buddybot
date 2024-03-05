<div class="p-5">

<?php
$mo_addfile_page = new \MetagaussOpenAI\Admin\Html\Views\AddFile();
$mo_addfile_page->getHtml();
add_action('admin_footer', array($mo_addfile_page, 'pageJs'));

$mo_addfile_requests = new \MetagaussOpenAI\Admin\Requests\AddFile();
add_action('admin_footer', array($mo_addfile_requests, 'requestsJs'));
?>

</div>