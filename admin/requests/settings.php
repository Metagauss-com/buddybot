<?php

namespace MetagaussOpenAI\Admin\Requests;

final class Settings extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->pageVarsJs();
        $this->sectionToggleJs();
        $this->saveOptionsJs();
        $this->getGeneralOptionsJs();
        $this->toggleErrorsJs();
        $this->getOpenAiApiKeyJs();
    }

    protected function pageVarsJs()
    {
        echo '
        const optionsData = {};
        let dataErrors = [];
        let errorMessage = "";
        ';
    }

    private function sectionToggleJs()
    {
        echo '
        sectionToggle();
        $("#mgao-settings-section-select").change(sectionToggle);

        function sectionToggle() {
            $("#mgoa-settings-section-options > tbody").html("");
            $("#mgoa-settings-section-options-loader").removeClass("visually-hidden");
            let section = $("#mgao-settings-section-select").val();

            const data = {
                "action": "getOptions",
                "section": section,
                "nonce": "' . wp_create_nonce('get_options') . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#mgoa-settings-section-options-loader").addClass("visually-hidden");
                $("#mgoa-settings-section-options > tbody").html(response);
            });
        }
        ';
    }

    private function saveOptionsJs()
    {
        echo '
        $("#mgoa-settings-update-btn").click(saveOptions);

        function saveOptions() {

            getGeneralOptions();

            if (dataErrors.length > 0) {
                displayErrors();
                disableFields(false);
                hideBtnLoader("#mgao-chatbot-save-btn");
                return;
            }

            const data = {
                "action": "saveSettings",
                "chatbot_data": optionsData,
                "nonce": "' . wp_create_nonce('save_settings') . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . admin_url() . 'admin.php?page=metagaussopenai-chatbot&chatbot_id=' . '" + response.chatbot_id + "&success=1");
                } else {
                    dataErrors = response.errors;
                    displayErrors();
                }

                disableFields(false);
                hideBtnLoader("#mgao-chatbot-save-btn");
            });
        }
        ';
    }

    private function getGeneralOptionsJs()
    {
        echo '
        function getGeneralOptions() {
            if ($("#mgao-settings-section-select").val() === "general") {
                optionsData["openai_api_key"] = getOpenAiApiKey();
            }
        }
        ';
    }

    protected function toggleErrorsJs()
    {
        echo '
        displayErrors();
        function displayErrors() {
            let errorsHtml = "";

            if (dataErrors.length === 0) {
                $("#mgoa-settings-errors").hide();
                return;
            }

            $("#mgoa-settings-success").hide();
            $.each(dataErrors, function(index, value){
                errorsHtml = errorsHtml + "<li>" + value + "</li>";
            });

            $("#mgoa-settings-errors-list").html(errorsHtml);
            $("#mgoa-settings-errors").show();
            dataErrors.length = 0;
        }
        ';
    }

    private function getOpenAiApiKeyJs()
    {
        echo '
        function getOpenAiApiKey() {
            let key = $("#mgoa-settings-openai-api-key").val();
            key = $.trim(key);

            if (key === "") {
                dataErrors.push("' . __('OpenAI API Key cannot be empty.', 'metagauss-openai') . '"); 
            }

            return key;
        }
        ';
    }
}