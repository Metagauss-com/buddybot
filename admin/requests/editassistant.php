<?php

namespace BuddyBot\Admin\Requests;

final class EditAssistant extends \BuddyBot\Admin\Requests\MoRoot
{
    protected $assistant_id = '';

    protected function setAssistantId()
    {
        if (!empty($_GET['assistant_id'])) {
            $this->assistant_id = sanitize_text_field($_GET['assistant_id']);
        }
    }

    public function requestJs()
    {
        $this->setVarsJs();
        $this->getModelsJs();
        $this->assistantDataJs();
        $this->createAssistantJs();
        $this->loadAssistantValuesJs();
        $this->backBtnJs();
    }

    private function setVarsJs()
    {
        $context = 'create';

        if ($this->assistant_id !== '') {
            $context = 'update';
        }

        echo '
        const context = "' . esc_js($context) . '";
        ';
    }

    private function getModelsJs()
    {
        $nonce = wp_create_nonce('get_models');
        echo '
        getModels();
        function getModels(){
            const select = $("#buddybot-editassistant-assistantmodel");
            const data = {
                "action": "getModels",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.html(response.html);
                    select.siblings(".buddybot-dataload-spinner").hide();
                    if (context === "update") {
                        getAssistantData();
                    }

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
        function assistantData() {
            let assistantData = {};
            assistantData["name"] = $("#buddybot-editassistant-assistantname").val();
            assistantData["description"] = $("#buddybot-editassistant-assistantdescription").val();
            assistantData["model"] = $("#buddybot-editassistant-assistantmodel").val();
            //assistantData["tools"] = assistantTools();
            assistantData["friendly_name"] = $("#buddybot-editassistant-nameinstruction").val();
            assistantData["aditional_instructions"] = $("#buddybot-editassistant-aditionalinstructions").val();
            assistantData["temperature"] = parseFloat($("#buddybot-editassistant-assistanttemperature-range").val());
            assistantData["top_p"] = parseFloat($("#buddybot-editassistant-assistanttopp-range").val());
            

            return assistantData;
        }

        // function assistantTools() {
        //     let assistantTools = [];

        //     $("#buddybot-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
        //         if ($(this).is(":checked")) {
        //             let value = $(this).val();
        //             assistantTools.push(value);
        //         }
        //     });

        //     return assistantTools;
        // }

        function assistantFiles() {
            let assistantFiles = [];

            $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
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
        $nonce = wp_create_nonce('create_assistant');
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = isset($vectorstore_data['id']) ? $vectorstore_data['id'] : '';
        echo '
        $("#buddybot-editassistant-editassistant-submit").click(createAssistant);

        function createAssistant(){
            hideAlert();
            disableFields(true);
            showBtnLoader("#buddybot-editassistant-editassistant-submit");
            let aData = assistantData();
            let vectorStoreId = "' . esc_js($vectorstore_id) . '";

            const data = {
                "action": "createAssistant",
                "assistant_id": "' . esc_js($this->assistant_id) . '",
                "assistant_data": JSON.stringify(aData),
                "vectorstore_id": vectorStoreId,
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                hideBtnLoader("#buddybot-editassistant-editassistant-submit");
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . esc_url(get_admin_url()) . 'admin.php?page=buddybot-editassistant&assistant_id=' . '" + response.result.id);
                } else {
                    showAlert(response.message);
                }
                disableFields(false);
            });
        };
        ';
    }

    private function loadAssistantValuesJs()
    {
        if ($this->assistant_id === null or $this->assistant_id === '') {
            return;
        }

        $nonce = wp_create_nonce('get_assistant_data');
        echo '



        function getAssistantData(){
            disableFields(true);
            const data = {
                "action": "getAssistantData",
                "assistant_id": "' . esc_js($this->assistant_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    fillAssistantValues(response.result);
                } else {
                    showAlert(response.message);
                }

                disableFields(false);
                //recountFiles();
            });
        };

        function fillAssistantValues(assistant) {
            $("#buddybot-editassistant-assistantname").val(assistant.name);
            $("#buddybot-editassistant-assistantdescription").val(assistant.description);
            $("#buddybot-editassistant-assistantmodel").val(assistant.model);
            //$("#buddybot-editassistant-assistantinstructions").val(assistant.instructions);
            $("#buddybot-editassistant-assistanttemperature-range").val(assistant.temperature);
            $("#buddybot-editassistant-assistanttemperature-value").text(assistant.temperature);
            $("#buddybot-editassistant-assistanttopp-range").val(assistant.top_p.toFixed(1));
            $("#buddybot-editassistant-assistanttopp-value").text(assistant.top_p.toFixed(1));
            checkEnabledTools(assistant.tools);
            // selectAttachedFiles(assistant.file_ids);

            if (assistant.metadata) {
                $("#buddybot-editassistant-nameinstruction").val(assistant.metadata.buddybot_friendly_name);
                $("#buddybot-editassistant-aditionalinstructions").val(assistant.metadata.buddybot_user_instructions);
            }
        }

        function checkEnabledTools(tools) {
            
            let cbValues = [];
            
            $.each(tools, function(index, tool) {
                cbValues.push(tool.type);
            });

            if (cbValues.length === 0) {
                return;
            }

            $("#buddybot-editassistant-assistanttools").find("input[type=checkbox]").each(function(){
                
                let cbValue = $(this).val();

                if ($.inArray(cbValue, cbValues) > -1) {
                    $(this).prop("checked", true);
                }
            
            });
        }

        // function selectAttachedFiles(fileIds) {

        //     if (fileIds.length === 0 || !$.isArray(fileIds)) {
        //         return;
        //     }

        //     $("#buddybot-editassistant-assistantfiles").find("input[type=checkbox]").each(function(){
                
        //         let fileId = $(this).val();
                
        //         if ($.inArray(fileId, fileIds) > -1) {
        //             $(this).prop("checked", true);
        //         }

        //         filesCount();

        //     });
        // }
        ';
    }

    private function backBtnJs()
    {
        $path = 'admin.php?page=buddybot-assistants';
        echo'
        $("#buddybot-editassistant-back").on("click", function() {
            window.location.href = "' . esc_url($path) . '";
        });
        ';
    }
}