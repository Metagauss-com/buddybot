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
        $this->buddybotDataJs();
        $this->saveBuddyBotJs();
        $this->loadAssistantValuesJs();
        $this->openaiSearchMsgJs();
        $this->sampleInstructionsModalJs();
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
        $(".copy-btn").click(function () {
            let button = $(this);
            let originalIcon = button.html();
            button.prop("disabled", true);
            let textToCopy = button.attr("data-text");
            let copiedText = "' . esc_html__("Copied!", "buddybot-ai-custom-ai-assistant-and-chat-agent") .'"; 

            navigator.clipboard.writeText(textToCopy).then(() => {
                button.html(copiedText);

                setTimeout(() => {
                    button.html(originalIcon);
                    button.prop("disabled", false);
                }, 2000);
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
            const select = $("#buddybot-assistantmodel");
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
                    if(response.empty_key) {
                        select.html(response.html);
                    }
                    select.siblings(".buddybot-dataload-spinner").hide();
                    showAlert(response.message);
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
            buddybotData["assistant_name"] = $("#buddybot-assistantname").val();
            buddybotData["assistant_model"] = $("#buddybot-assistantmodel").val();
            buddybotData["additional_instructions"] = $("#buddybot-additionalinstructions").val();
            buddybotData["assistant_temperature"] = $("#buddybot-assistanttemperature-range").val();
            buddybotData["assistant_topp"] = $("#buddybot-assistanttopp-range").val();
            buddybotData["openai_search"] = $("#buddybot-openaisearch").is(":checked") ? "1" : "0";
            buddybotData["openaisearch_msg"] = $("#buddybot-openaisearch-msg").val();
            buddybotData["personalized_options"] = $("#buddybot-personalizedoptions").is(":checked") ? "1" : "0";
            //buddybotData["fallback_behavior"] = $("#buddybot-fallbackbehavior").val();
            buddybotData["emotion_detection"] = $("#buddybot-emotiondetection").is(":checked") ? "1" : "0";
            buddybotData["greeting_message"] = $("#buddybot-greetingmessage").val();
            //buddybotData["multilingual_support"] = $("#buddybot-multilingualsupport").is(":checked") ? "1" : "0";
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
            disableFields(true);
            showWordpressLoader("#buddybot-buddybotsubmit");
            let aData = buddybotData(); 

            const data = {
                "action": "saveBuddyBot",
                "buddybot_data": aData,
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.replace("' . esc_url(get_admin_url()) . 'admin.php?page=buddybot-editchatbot&chatbot_id=' . '" + response.chatbot_id);
                } else {
                    showAlert(response.message);
                }
                hideWordpressLoader("#buddybot-buddybotsubmit");
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
                "buddybot_id": "' . esc_js($this->buddybot_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                
                if (response.success) {
                    fillAssistantValues(response.result, response.local);
                } else {
                    showAlert(response.message);
                }

                disableFields(false);
            });
        };

        function fillAssistantValues(assistant, buddybot) {
            $("#buddybot-buddybotname").val(buddybot.chatbot_name);
            $("#buddybot-buddybotdescription").val(buddybot.chatbot_description);
            $("#buddybot-assistantname").val(assistant.name);
            $("#buddybot-assistantmodel").val(assistant.model);
            $("#buddybot-assistanttemperature-range").val(assistant.temperature);
            $("#buddybot-assistanttemperature-value").text(assistant.temperature);
            $("#buddybot-assistanttopp-range").val(assistant.top_p);
            $("#buddybot-assistanttopp-value").text(assistant.top_p);
            $("#buddybot-openaisearch").prop("checked", buddybot.openai_search == 1);
            $("#buddybot-personalizedoptions").prop("checked", buddybot.personalized_options == 1);
            //$("#buddybot-fallbackbehavior").val(buddybot.fallback_behavior);
            $("#buddybot-emotiondetection").prop("checked", buddybot.emotion_detection == 1);
            $("#buddybot-greetingmessage").val(buddybot.greeting_message);
           // $("#buddybot-multilingualsupport").prop("checked", buddybot.multilingual_support == 1);

            if (assistant.metadata) {
                $("#buddybot-additionalinstructions").val(assistant.metadata.aditional_instructions);
                $("#buddybot-openaisearch-msg").val(assistant.metadata.openaisearch_msg);
            }

            if ($("#buddybot-openaisearch").is(":checked")) {
                showHide($("#buddybot-openaisearch")[0], "buddybot-openaisearch-childfieldrow", "", "");
            }
        }
        ';
    }

    private function openaiSearchMsgJs()
    {
        echo'     
        $("#buddybot-openaisearch").on("change", function () {
            showHide(this, "buddybot-openaisearch-childfieldrow", "", "");
        });
        ';
    }
}