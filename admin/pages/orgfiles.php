<div class="p-5">

<?php
$mo_files_page = new \MetagaussOpenAI\Admin\Html\Views\OrgFiles();
$mo_files_page->getHtml();

$mo_files_requests = new \MetagaussOpenAI\Admin\Requests\OrgFiles();
add_action('admin_footer', array($mo_files_requests, 'requestsJs'));
?>

</div>