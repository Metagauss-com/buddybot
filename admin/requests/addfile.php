<?php

namespace MetagaussOpenAI\Admin\Requests;

class AddFile extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->addFileJs();
    }

    private function addFileJs()
    {
        $nonce = wp_create_nonce('add_file');
        
        echo '
        $("#metagauss-openai-file-upload-btn").click(addFile);

        function addFile() {

            let userFile = $("#metagauss-openai-file-upload").prop("files")[0];

            const data = {
                "action": "addFile",
                "file": userFile,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                alert(response);
            });
        }
        ';
    }
}