<?php

namespace BuddyBot\Admin\Requests;

final class EditChatBot extends \BuddyBot\Admin\Requests\MoRoot
{
    protected $assistant_id = '';
    protected $buddybot_id = 0;

    protected function setAssistantId()
    {
        if (isset($_GET['chatbot_id'])) {
    
            $sql = \BuddyBot\Admin\Sql\EditChatBot::getInstance();
            $chatbot = $sql->getItemById('chatbot', absint($_GET['chatbot_id']));

            if (is_object($chatbot)) {
                $this->assistant_id = isset($chatbot->assistant_id) ? $chatbot->assistant_id : '';
            }
        }
    }

    protected function setBuddybotId()
    {
        if (isset($_GET['chatbot_id']) && !empty($_GET['chatbot_id'])) {
            $this->buddybot_id = absint($_GET['chatbot_id']);
        }

    }

    public function requestJs()
    {
        $this->setVarsJs();
        $this->getModelsJs();
        $this->selectAssistantModalJs();
        $this->buddybotDataJs();
        $this->saveBuddyBotJs();
        $this->loadAssistantValuesJs();
        $this->editChatbotshowHideJs();
        $this->sampleInstructionsModalJs();
    }

    protected function selectAssistantModalJs()
    {
        $this->getAssistantsListJs();
        $this->selectAssistantJs();
        $this->showLoadMoreBtnJs();
        $this->updateLastIdJs();
        $this->loadMoreBtnJs();
        $this->parseAssistantListDataJs();
        $this->highlightCurrentAssistantJs();
        $this->autoScrollJs();
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

    private function sampleInstructionsModalJs()
    {
        echo'
        $(".buddybot-btn").on("click", function () {
        let button = $(this);
        let textToCopy = button.attr("data-text");

        navigator.clipboard.writeText(textToCopy).then(() => {
            button.addClass("buddybot-copied");

            setTimeout(() => {
                button.removeClass("buddybot-copied");
            }, 1500);
        });
    });
        ';
    }

    private function getModelsJs()
    {
        $nonce = wp_create_nonce('get_models');
        echo '
        getModels();
        function getModels(){
            disableTableFields("buddybot-table", true);
            const select = $("#buddybot-assistantmodel");
            const data = {
                "action": "getModels",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.html(response.html);
                    select.siblings("#buddybot-assistant-model-spinner").hide();
                    if (context === "update") {
                        getAssistantData();
                    } else {
                        disableTableFields("buddybot-table", false);
                    }

                } else {
                    disableTableFields("buddybot-table", false);
                    if(response.empty_key) {
                        select.html(response.html);
                    }
                    select.siblings("#buddybot-assistant-model-spinner").hide();
                    showAlert(response.message);
                    $("#buddybot-settings-success").hide();
                }
            });
        };
        ';
    }

    private function buddybotDataJs()
    {
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = isset($vectorstore_data['id']) ? $vectorstore_data['id'] : '';
        echo '
        function buddybotData() {
            let buddybotData = {};
            
            buddybotData["buddybot_name"] = $("#buddybot-buddybotname").val();
            buddybotData["buddybot_description"] = $("#buddybot-buddybotdescription").val();
            buddybotData["existing_assistant"] = $("#buddybot-existing-assistant").is(":checked") ? "1" : "0";
 
            if (buddybotData["existing_assistant"] === "1") {
                buddybotData["assistant_model"] = $("#buddybot-existing-assistant-model").data("model");
                buddybotData["connect_assistant"] = $("#buddybot-assistant-id").val();
            } else {
                buddybotData["assistant_name"] = $("#buddybot-assistantname").val();
                buddybotData["assistant_model"] = $("#buddybot-assistantmodel").val();
                buddybotData["additional_instructions"] = $("#buddybot-additionalinstructions").val();
                buddybotData["assistant_temperature"] = $("#buddybot-assistanttemperature-range").val();
                buddybotData["temp_topp"] = $("#buddybot-assistanttemperature-range").val() + "_" + $("#buddybot-assistanttopp-range").val();
                buddybotData["assistant_topp"] = $("#buddybot-assistanttopp-range").val();
                buddybotData["openai_search"] = $("#buddybot-openaisearch").is(":checked") ? "1" : "0";

                if (buddybotData["openai_search"] === "1") {
                    buddybotData["openaisearch_msg"] = $("#buddybot-openaisearch-msg").val();
                }

                buddybotData["emotion_detection"] = $("#buddybot-emotiondetection").is(":checked") ? "1" : "0";
                buddybotData["greeting_message"] = $("#buddybot-greetingmessage").val();
                // buddybotData["multilingual_support"] = $("#buddybot-multilingualsupport").is(":checked") ? "1" : "0";
                // buddybotData["response_length"] = $("#buddybot-openai-response-length").val();
            }

            buddybotData["assistant_id"] ="' . esc_js($this->assistant_id) . '";
            buddybotData["buddybot_id"] ="' . esc_js($this->buddybot_id) . '";
            buddybotData["vectorstore_id"] ="' . esc_js($vectorstore_id) . '";
            

            return buddybotData;
        }
        ';
    }

    private function saveBuddyBotJs()
    {
        $nonce = wp_create_nonce('save_buddybot');
        echo '
        $("#buddybot-buddybotsubmit").click(saveBuddyBot);

        function saveBuddyBot(){
            hideAlert();
            $("#buddybot-buddybotsubmit").prop("disabled", true);
            $("#buddybot-buddybotsubmit").closest(".buddybot-btn-wrap").find(".spinner").show();
            let aData = buddybotData(); 

            const data = {
                "action": "saveBuddyBot",
                "buddybot_data": JSON.stringify(aData),
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . esc_url(get_admin_url()) . 'admin.php?page=buddybot-editchatbot&chatbot_id=' . '" + response.chatbot_id + "&success=1");
                } else {
                    showAlert(response.message);
                    $("#buddybot-settings-success").hide();
                }
                $("#buddybot-buddybotsubmit").prop("disabled", false);
                $("#buddybot-buddybotsubmit").closest(".buddybot-btn-wrap").find(".spinner").hide();
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
            const data = {
                "action": "getAssistantData",
                "assistant_id": "' . esc_js($this->assistant_id) . '",
                "buddybot_id": "' . esc_js($this->buddybot_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    fillAssistantValues(response.result, response.local);
                    $("#buddybot-assistant-details-container").html(response.html);
                } else {
                    showAlert(response.message);
                    $("#buddybot-settings-success").hide();
                }

                disableTableFields("buddybot-table", false);
            });
        };

        function fillAssistantValues(assistant, buddybot) {
            $("#buddybot-buddybotname").val(buddybot.chatbot_name);
            $("#buddybot-buddybotdescription").val(buddybot.chatbot_description);
            $("#buddybot-existing-assistant").prop("checked", buddybot.external == 1);
            if ($("#buddybot-existing-assistant").is(":checked")) {
                    $(".buddybot-conditional-settings").hide();
                    showHide($("#buddybot-existing-assistant")[0], "buddybot-existing-assistant-childfieldrow", "", "");
                    $("#buddybot-assistant-id").val(assistant.id);
            } else {
            
                $("#buddybot-assistantname").val(assistant.name);
                $("#buddybot-assistantmodel").val(assistant.model);
                $("#buddybot-assistanttemperature-range").val(assistant.temperature);
                $("#buddybot-assistanttemperature-value").text(assistant.temperature);
                $("#buddybot-assistanttopp-range").val(assistant.top_p);
                $("#buddybot-assistanttopp-value").text(assistant.top_p);
                $("#buddybot-openaisearch").prop("checked", buddybot.openai_search == 1);
                $("#buddybot-emotiondetection").prop("checked", buddybot.emotion_detection == 1);
                $("#buddybot-greetingmessage").val(buddybot.greeting_message);
                $("#buddybot-multilingualsupport").prop("checked", buddybot.multilingual_support == 1);
            
                if (assistant.metadata) {
                    $("#buddybot-additionalinstructions").val(assistant.metadata.aditional_instructions);
                    $("#buddybot-openaisearch-msg").val(assistant.metadata.openaisearch_msg);
                    $("#buddybot-openai-response-length").val(assistant.metadata.response_length);
                }

                if ($("#buddybot-openaisearch").is(":checked")) {
                    showHide($("#buddybot-openaisearch")[0], "buddybot-openaisearch-childfieldrow", "", "");
                }
            }
        }
        ';
    }

    private function editChatbotshowHideJs()
    {
        echo'     
        $("#buddybot-openaisearch").on("change", function () {
            showHide(this, "buddybot-openaisearch-childfieldrow", "", "");
        });

        $("#buddybot-existing-assistant").on("change", function () {
            showHide(this, "buddybot-existing-assistant-childfieldrow", "", "");
        });

        $(document).on("change", "#buddybot-existing-assistant", function() {
            if ($(this).is(":checked")) {
                $(".buddybot-conditional-settings").hide();
            } else {
                $(".buddybot-conditional-settings").show();
            }
        });

    $(document).on("click", ".toggle-more-details", function () {
    console.log("clicked");
            const $btn = $(this);
            const $details = $(".buddybot-more-details");

            $details.slideToggle(200, function () {
                const showText = $btn.data("show");
                const hideText = $btn.data("hide");

                if ($details.is(":visible")) {
                    $btn.addClass("active").text(hideText);
                } else {
                    $btn.removeClass("active").text(showText);
                }
            });
        });
        ';
    }

    protected function getAssistantsListJs()
    {
        echo '
        const selectAssistantModal = document.getElementById("buddybot-select-assistant-modal");
        if (selectAssistantModal) {
            $(document).on("click", \'[data-modal="buddybot-select-assistant-modal"]\', function() {

                $("#buddybot-select-assistant-modal-list").html("");
                $("#buddybot-selectassistant-spinner").show();
                $("#buddybot-selectassistant-load-more-btn").hide();
                
                
                const data = {
                    "action": "selectAssistantModal",
                    "nonce": "' . esc_js(wp_create_nonce('select_assistant_modal')) . '"
                };

                $.post(ajaxurl, data, function(response) {
                    parseAssistantListData(response);
                });
            });
        }
        ';
    }

    protected function selectAssistantJs()
    {
        echo '
        $("#buddybot-select-assistant-modal-list").on("click", ".buddybot-list-group-item", function(){
            
            let assistantId = $(this).attr("data-mgao-id");
            $("#buddybot-assistant-id").val(assistantId);
            $("#buddybot-select-assistant-modal").removeClass("show");
            $("#buddybot-assistant-details-container").html("");
        $("#buddybot-assistant-details-spinner").addClass("is-active").css("display", "inline-block");

            const data = {
                "action": "selectedAssistant",
                "assistant_id": assistantId,
                "nonce": "' . esc_js(wp_create_nonce('selected_assistant')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                $("#buddybot-assistant-details-container").html(response.html);
                $("#buddybot-assistant-details-spinner").hide();
                } else {
                    ShowAlert(response.message);
                    $("#buddybot-assistant-details-spinner").hide();
                }

            });
            
        });
        ';
    }

    protected function showLoadMoreBtnJs()
    {
        echo '
        showLoadMoreBtn();
        function showLoadMoreBtn(hasMore = false) {
            if (hasMore) {
                $("#buddybot-selectassistant-load-more-btn").show();
            } else {
                $("#buddybot-selectassistant-load-more-btn").hide();
            }
        }
        ';
    }

    protected function updateLastIdJs()
    {
        echo '
        function updateLastId(lastId) {
            $("#buddybot-selectassistant-last-id").val(lastId);
        }
        ';
    }

    protected function loadMoreBtnJs()
    {
        echo '
        $("#buddybot-selectassistant-load-more-btn").click(loadMoreBtn);
        function loadMoreBtn() {
            autoScroll();
            showWordpressLoader("#buddybot-selectassistant-load-more-btn");
            $("#buddybot-selectassistant-spinner").removeClass("visually-hidden");
            const data = {
                "action": "selectAssistantModal",
                "after": $("#buddybot-selectassistant-last-id").val(),
                "nonce": "' . esc_js(wp_create_nonce('select_assistant_modal')) . '"
            };

            $.post(ajaxurl, data, function(response) {
                parseAssistantListData(response);
                hideWordpressLoader("#buddybot-selectassistant-load-more-btn"); 
            });
        }
        ';
    }

    protected function parseAssistantListDataJs()
    {
        echo '
        function parseAssistantListData(listData) {
            listData = JSON.parse(listData);
            if (listData.success) {
                $("#buddybot-selectassistant-spinner").hide();
                $("#buddybot-select-assistant-modal-list").append(listData.html);
                showLoadMoreBtn(listData.result.has_more);
                updateLastId(listData.result.last_id);
                highlightCurrentAssistant();
            } else {
                $("#buddybot-selectassistant-spinner").hide();
                $("#buddybot-select-assistant-modal-list").append(listData.message);
            }
        }
        ';
    }

    protected function highlightCurrentAssistantJs()
    {
        echo '
        function highlightCurrentAssistant() {
            let currentAssistantId = $("#buddybot-assistant-id").val();
            if (currentAssistantId !== "") {
                $("#buddybot-select-assistant-modal-list").find("[data-mgao-id=" + currentAssistantId).addClass("buddybot-text-bg-dark");
            }
        }
        ';
    }

    protected function autoScrollJs()
    {
        echo '
        function autoScroll() {
            count = $("#buddybot-select-assistant-modal-list").length;
            $("#buddybot-select-assistant-modal-list").parent().animate({
                scrollTop: $("#buddybot-selectassistant-spinner").offset().top
            }, 1000);
        }
        ';
    }
}