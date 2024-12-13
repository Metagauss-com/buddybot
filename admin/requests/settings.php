<?php

namespace BuddyBot\Admin\Requests;

final class Settings extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->pageVarsJs();
        $this->sectionToggleJs();
        $this->saveOptionsJs();
        //$this->getGeneralOptionsJs();
        $this->toggleErrorsJs();
        $this->getOpenAiApiKeyJs();
        $this->createVectorStore();
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
            $("#buddybot-settings-section-options > tbody").html("");
            $("#buddybot-settings-section-options-loader").removeClass("visually-hidden");
            let section = $("#mgao-settings-section-select").val();

            const data = {
                "action": "getOptions",
                "section": section,
                "nonce": "' . esc_js(wp_create_nonce('get_options')) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-settings-section-options-loader").addClass("visually-hidden");
                $("#buddybot-settings-section-options > tbody").html(response);
            });
        }
        ';
    }

    private function saveOptionsJs()
    {
        echo '
        $("#buddybot-settings-update-btn").click(saveOptions);

        function saveOptions() {
         showBtnLoader("#buddybot-settings-update-btn");
           getOpenAiApiKey();
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
                $("#buddybot-settings-errors").hide();
                return;
            }

            $("#buddybot-settings-success").hide();
            $.each(dataErrors, function(index, value){
                errorsHtml = errorsHtml + "<li>" + value + "</li>";
            });

            $("#buddybot-settings-errors-list").html(errorsHtml);
            $("#buddybot-settings-errors").show();
            dataErrors.length = 0;
        }
        ';
    }

    private function getOpenAiApiKeyJs()
    {
        echo '
        function getOpenAiApiKey() {
            let key = $("#buddybot-settings-openai-api-key").val();
            key = $.trim(key);

            if (key === "") {
                dataErrors.push("' . esc_html(__('OpenAI API Key cannot be empty.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '");
                displayErrors(); 
                hideBtnLoader("#buddybot-settings-update-btn");
            } else{ 
                verifyOpenaiApiKey(key);
            }
                
        }
            function verifyOpenaiApiKey(apiKey) {

                const data = {
                    "action": "verifyApiKey",
                    "api_key": apiKey,
                    "nonce": "' . esc_js(wp_create_nonce('verify_api_key')) . '"
                };

                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        checkVectorStore(apiKey);
                       // saveOpenaiApiKey(apiKey);
                    }else{
                        dataErrors.push(response.message);
                        displayErrors();
                        hideBtnLoader("#buddybot-settings-update-btn");
                    }
                });
            }

            function saveOpenaiApiKey(apiKey){
                const section = $("#mgao-settings-section-select").val();
                
                optionsData["openai_api_key"] = apiKey;

                const data = {
                    "action": "saveSettings",
                    "options_data": JSON.stringify(optionsData),
                    "section": section,
                    "nonce": "' . esc_js(wp_create_nonce('save_settings')) . '"
                };

                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        location.replace("' . esc_url(admin_url()) . 'admin.php?page=buddybot-settings&section=' . '" + section + "&success=1");
                    } else {
                        $("#buddybot-settings-error-message").html(response.message);
                        dataErrors = response.errors;
                        displayErrors();
                    }

                    disableFields(false);
                    hideBtnLoader("#buddybot-settings-update-btn");
                });
            }
        ';
    }

    private function createVectorStore()
    {
        $nonce = wp_create_nonce('create_vectorstore');
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $hostname = wp_parse_url(home_url(), PHP_URL_HOST);
        echo '
            //$("#buddybot-vectorstore-create").click(checkVectorStore);

            function checkVectorStore(apiKey){
                const vectorStoreData = ' . wp_json_encode($vectorstore_data) . ';
                if (vectorStoreData && vectorStoreData.id) {
                    saveOpenaiApiKey(apiKey);
                } else {
                    createVectorStore(apiKey);
                }
            }

            function createVectorStore(apiKey){
                let storeData = vectorstoreData();

                const data = {
                    "action": "createVectorStore",
                    "api_key": apiKey,
                    "vectorstore_data": JSON.stringify(storeData),
                    "nonce": "' . esc_js($nonce) . '"
                };
        
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                    } else {
                    }
                    saveOpenaiApiKey(apiKey);
                });
            }
                function vectorstoreData() {
                let vectorstoreData = {};
                vectorstoreData["name"] = "' . esc_js($hostname) . '";

                return vectorstoreData;
            }

        ';
    }
}