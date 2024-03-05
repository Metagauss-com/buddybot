<?php

namespace MetagaussOpenAI\Admin\Requests;

class OrgFiles extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->getOrgFilesJs();
    }

    private function getOrgFilesJs()
    {
        $nonce = wp_create_nonce('get_org_files');
        echo '
        getOrgFiles();
        function getOrgFiles() {

            const data = {
                "action": "getOrgFiles",
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("tbody").html(response);
            });
        }
        ';
    }
}