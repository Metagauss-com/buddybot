<?php

namespace BuddyBot\Admin\Requests;

class BuddyBots extends \BuddyBot\Admin\Requests\MoRoot
{

    public function requestJs()
    {
        $this->getBuddyBotsJs();
        $this->paginationDropdownJs();
        $this->getModelsJs();
    }

    private function getBuddyBotsJs()
    {
        $nonce = wp_create_nonce('get_buddybots');
        echo '
        //getBuddyBots();
        function getBuddyBots(paged = 1){

            const data = {
                "action": "getBuddyBots",
                "paged": paged,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    $("#buddybot-assistants-loading-spinner").addClass("visually-hidden");
                    $(".buddybot-org-buddybots-table tbody").append(response.html);

                    // if (response.result.has_more) {
                    //     $("#buddybot-assistants-load-more-btn").removeClass("visually-hidden");
                    //     $("#buddybot-assistants-load-more-btn").prop("disabled", false);
                    //     $("#buddybot-assistants-load-more-btn").find(".buddybot-loaderbtn-label").removeClass("visually-hidden");
                    //     $("#buddybot-assistants-load-more-btn").find(".buddybot-loaderbtn-spinner").addClass("visually-hidden");
                    // } else {
                    //     $("#buddybot-assistants-load-more-btn").addClass("visually-hidden");
                    //     $("#buddybot-assistants-no-more").removeClass("visually-hidden");
                    // }

                } else {
                    showAlert(response.message);
                }
            });
        }
        ';
    }

    private function paginationDropdownJs()
    {
        $nonce = wp_create_nonce('pagination_dropdown');
        echo'
        $("#buddybot-chatbot-pagination").on("change", function() {

			var selectedValue = $(this).val();

			const data = {
				"action": "savePaginationLimit",
				"selected_value": selectedValue,
				"nonce": "' . esc_js($nonce) . '"
			};

			$.post(ajaxurl, data, function(response) {
				response = JSON.parse(response);

				if (response.success) {
					location.reload();
				} else {
                    showAlert(response.message);
                }
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
            const select = $("#buddybot-filter-model");
            const data = {
                "action": "getModels",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.find("option:disabled").remove();
                    select.append(response.html);
                    // select.siblings(".buddybot-dataload-spinner").hide();
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    private function deleteBuddybot()
    {
        $nonce = wp_create_nonce('delete_buddybot');
        echo'

        //let threadId;
        $("#buddybot-delete-conversation-cancel-btn").on("click", function() {
            // hideAlert();
			// threadId = $(this).attr("data-buddybot-itemid");
				
			$(".buddybot-conversation-delete").prop("disabled", true);
			$("#buddybot-delete-conversation-modal").modal("show");
			
		});

        $("#buddybot-delete-conversation-cancel-btn").on("click", function() {
			$(".buddybot-conversation-delete").prop("disabled", false);
		}); 

        $("#buddybot-confirm-conversation-delete-btn").click(function(){
            $("#buddybot-delete-conversation-cancel-btn").prop("disabled", true);
            $("#buddybot-confirm-conversation-delete-btn").prop("disabled", true);
            $("#buddybot-deleting-conversation-msg").show();

            const data = {
                "action": "deleteConversation",
                "thread_id": threadId,
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("tr.buddybot-conversations-table-row[data-buddybot-itemid=" + threadId + "]").remove();
                    renumberRows();
                } else {
                    $("#buddybot-delete-conversation-modal").modal("hide");
                    showAlert(response.message);
                }
                $("#buddybot-delete-conversation-modal").modal("hide");
                $("#buddybot-delete-conversation-cancel-btn").prop("disabled", false);
                $("#buddybot-confirm-conversation-delete-btn").prop("disabled", false);
                $("#buddybot-deleting-conversation-msg").hide();
                $(".buddybot-conversation-delete").prop("disabled", false);
            });
        });
        ';
    }
}