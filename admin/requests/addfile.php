<?php

namespace BuddyBot\Admin\Requests;

class AddFile extends \BuddyBot\Admin\Requests\MoRoot
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

            let fileId = $("#metagauss-openai-file-selected").val();

            const data = {
                "action": "addFile",
                "file_id": fileId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                alert(response);
                response = JSON.parse(response);

                if (response.success) {
                    $("#metagauss-openai-file-output").html(response.html);
                }
            });
        }
        ';
    }
}