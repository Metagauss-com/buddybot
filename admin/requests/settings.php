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
        $this->changeKey();
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

        $(document).on("keypress", "#buddybot-settings-openai-api-key", function(e) {
            let key = e.key;
            if (key === "Enter") {
                saveOptions();
            }
        });

        function saveOptions() {
            const hiddenKey = $("#buddybot-settings-hidden-key").val();
            const textFieldValue = $("#buddybot-settings-openai-api-key").val();

            if (hiddenKey === textFieldValue) {
                showWordpressLoader("#buddybot-settings-update-btn");
                const section = $("#mgao-settings-section-select").val();
                location.replace("' . esc_url(admin_url()) . 'admin.php?page=buddybot-settings&section=' . '" + section + "&success=1");
            } else {
                showWordpressLoader("#buddybot-settings-update-btn");
                getOpenAiApiKey();
            }
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
                hideWordpressLoader("#buddybot-settings-update-btn");
            } else { 
                vectorStore(key);
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
                        vectorStore(apiKey);
                        saveOpenaiApiKey(apiKey);
                    } else {
                        dataErrors.push(response.message);
                        displayErrors();
                        hideWordpressLoader("#buddybot-settings-update-btn");
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
                    hideWordpressLoader("#buddybot-settings-update-btn");
                });
            }
        ';
    }

    private function createVectorStore()
    {
        $nonce = wp_create_nonce('auto_create_vectorstore');
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $hostname = wp_parse_url(home_url(), PHP_URL_HOST);
        echo '
            //$("#buddybot-vectorstore-create").click(checkVectorStore);

            function vectorStore(apiKey){
                const vectorStoreData = ' . wp_json_encode($vectorstore_data) . ';
                if (vectorStoreData && vectorStoreData.id) {
                    checkVectorStore(apiKey, vectorStoreData.id)
                    //saveOpenaiApiKey(apiKey);
                } else {
                    checkAllVectorStore(apiKey)
                }
            }

            function createVectorStore(apiKey){
                let storeData = vectorstoreData();

                const data = {
                    "action": "autoCreateVectorStore",
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

            function checkVectorStore(apiKey, vectorstore_id){
                const data = {
                    "action": "checkVectorStore",
                    "api_key": apiKey,
                    "vectorstore_id": vectorstore_id,
                    "nonce": "' . esc_js($nonce) . '"
                };
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        saveOpenaiApiKey(apiKey);
                    } else {
                        if(response.vectorstore_not_found){
                            checkAllVectorStore(apiKey);
                        } else {
                            dataErrors.push(response.message);
                            displayErrors();
                            hideWordpressLoader("#buddybot-settings-update-btn");
                        }
                    }
                });
            }

            function checkAllVectorStore(apiKey) {
                hideAlert();
                const data = {
                    "action": "checkAllVectorStore",
                    "api_key": apiKey,
                    "nonce": "' . esc_js($nonce) . '"
                };
  
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        saveOpenaiApiKey(apiKey);
                    } else {
                        if(response.create_vectorstore) {
                            createVectorStore(apiKey);
                        } else {
                            dataErrors.push(response.message);
                            displayErrors();
                            hideWordpressLoader("#buddybot-settings-update-btn");
                        }
                    }
                });
            }
        ';
    }

    private function changeKey()
    {
        echo'
        $(document).on("click", "#buddybot-settings-key-change-btn", function() {
            $("#buddybot-change-key-confirmation-modal").modal("show");
        });

        $(document).on("click", "#buddybot-change-key-confirm-btn", function() {
            $("#buddybot-settings-openai-api-key").prop("disabled", false); 
            $("#buddybot-settings-key-change-btn").prop("disabled", true);
            $("#buddybot-change-key-confirmation-modal").modal("hide");
        });

        $("#buddybot-change-key-confirmation-modal").on("hidden.bs.modal", function () {
            $(this).find(":focus").blur(); // Remove focus from any element inside the modal
            $("#buddybot-settings-key-change-btn").focus();
        });
        ';
    }
}