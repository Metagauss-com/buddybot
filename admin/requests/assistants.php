<?php

namespace BuddyBot\Admin\Requests;

final class Assistants extends \BuddyBot\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->getAssistantsJs();
        $this->loadMoreAssistantsJs();
        $this->deleteAssistantJs();
        $this->renumberRowsJs();
    }

    private function getAssistantsJs()
    {
        $nonce = wp_create_nonce('get_assistants');
        echo '
        getAssistants();
        function getAssistants(after = "") {

            const data = {
                "action": "getAssistants",
                "after": after,
                "current_count": $("table.buddybot-org-assistants-table").find("tr.buddybot-assistant-table-row").length,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    $("#buddybot-assistants-loading-spinner").addClass("visually-hidden");
                    $("tbody").append(response.html);

                    if (response.result.has_more) {
                        $("#buddybot-assistants-load-more-btn").removeClass("visually-hidden");
                        $("#buddybot-assistants-load-more-btn").prop("disabled", false);
                        $("#buddybot-assistants-load-more-btn").find(".buddybot-loaderbtn-label").removeClass("visually-hidden");
                        $("#buddybot-assistants-load-more-btn").find(".buddybot-loaderbtn-spinner").addClass("visually-hidden");
                    } else {
                        $("#buddybot-assistants-load-more-btn").addClass("visually-hidden");
                        $("#buddybot-assistants-no-more").removeClass("visually-hidden");
                    }

                } else {
                    showAlert(response.message);
                    $("#buddybot-assistants-loading-spinner").addClass("visually-hidden");
                    if(response.empty_key){
                        $("#buddybot-assistants-no-more").text("");
                        $("#buddybot-assistants-no-more").text("' . esc_html__('Assistants will be listed here once a valid OpenAI API key is saved in the settings.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '");
                        $("#buddybot-assistants-no-more").removeClass("visually-hidden");
                    }
                }
            });
        }
        ';
    }

    private function loadMoreAssistantsJs()
    {
        echo '
        $("#buddybot-assistants-load-more-btn").click(function() {
            let lastAssistant = $("tr.buddybot-assistant-table-row:last-child").attr("data-buddybot-itemid");
            $(this).prop("disabled", true);
            $(this).find(".buddybot-loaderbtn-label").addClass("visually-hidden");
            $(this).find(".buddybot-loaderbtn-spinner").removeClass("visually-hidden");
            getAssistants(lastAssistant);
        });
        ';
    }

    private function deleteAssistantJs()
    {
        $nonce = wp_create_nonce('delete_assistant');
        echo '
        $(".buddybot-org-assistants-table").on("click", ".buddybot-listbtn-assistant-delete", function(){
            
            $(".buddybot-listbtn-assistant-delete").prop("disabled", true);

            let assistantId = $(this).attr("data-buddybot-itemid");

            const data = {
                "action": "deleteAssistant",
                "assistant_id": assistantId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    $("tr.buddybot-assistant-table-row[data-buddybot-itemid=" + assistantId + "]").remove();
                    $(".buddybot-listbtn-assistant-delete").prop("disabled", false);
                    renumberRows();
                } else {
                    showAlert(response.message);
                }
            });
        });
        ';
    }

    private function renumberRowsJs()
    {
        echo '
        function renumberRows() {
            let i = 1;
            $("tr.buddybot-assistant-table-row").each(function() {
                $(this).children("th.buddybot-assistants-sr-no").html(i);
                i++;
            });
        }
        ';
    }
}