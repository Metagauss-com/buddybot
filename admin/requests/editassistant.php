<?php

namespace MetagaussOpenAI\Admin\Requests;

final class EditAssistant extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    protected $assistant_id = null;

    protected function setAssistantId()
    {
        if (!empty($_GET['assistant_id'])) {
            $this->assistant_id = sanitize_text_field($_GET['assistant_id']);
        }
    }

    public function requestJs()
    {
        $this->getModelsJs();
        $this->getFilesJs();
        $this->assistantDataJs();
        $this->createAssistantJs();
    }

    private function getModelsJs()
    {
        $nonce = wp_create_nonce('get_models');
        echo '
        getModels();
        function getModels(){
            const select = $("#mo-editassistant-assistantmodel");
            const data = {
                "action": "getModels",
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.html(response.html);
                    select.siblings(".mo-dataload-spinner").hide();
                }
            });
        };
        ';
    }

    private function getFilesJs()
    {
        $nonce = wp_create_nonce('get_files');
        echo '
        getFiles();
        function getFiles(){
            const data = {
                "action": "getFiles",
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#mo-editassistant-assistantfiles").html(response.html);
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    private function assistantDataJs()
    {
        echo '
        function assistantData()
        {
            let assistantData = {};
            assistantData["name"] = $("#mo-editassistant-assistantname").val();
            assistantData["description"] = $("#mo-editassistant-assistantdescription").val();
            assistantData["model"] = $("#mo-editassistant-assistantmodel").val();
            assistantData["instructions"] = $("#mo-editassistant-assistantinstructions").val();
            assistantData["tools"] = assistantTools();
            assistantData["file_ids"] = assistantFiles();

            return assistantData;
        }

        function assistantTools() {
            let assistantTools = [];

            $("#mo-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    let value = $(this).val();
                    assistantTools.push(value);
                }
            });

            return assistantTools;
        }

        function assistantFiles() {
            let assistantFiles = [];

            $("#mo-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                if ($(this).is(":checked")) {
                    let value = $(this).val();
                    assistantFiles.push(value);
                }
            });

            return assistantFiles;
        }
        ';
    }

    private function createAssistantJs()
    {
        if ($this->assistant_id !== null) {
            return;
        }

        $nonce = wp_create_nonce('create_assistant');
        echo '
        $("#mo-editassistant-editassistant-submit").click(createAssistant);

        function createAssistant(){
            hideAlert();
            let aData = assistantData();
            alert(JSON.stringify(aData));

            const data = {
                "action": "createAssistant",
                "assistant_data": JSON.stringify(aData),
                "nonce": "' . $nonce . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . get_admin_url() . 'admin.php?page=metagaussopenai-assistant&assistant_id=' . '" + response.result.id);
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }
}