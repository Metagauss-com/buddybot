<?php

namespace BuddyBot\Admin\Requests;

class BuddyBots extends \BuddyBot\Admin\Requests\MoRoot
{

    public function requestJs()
    {
        $this->deleteBuddyBotJs();
        $this->paginationDropdownJs();
    }

    private function deleteBuddyBotJs()
    {
        $nonce = wp_create_nonce('delete_buddybot');
        echo '

        let assistantId;
        let chatbotId;

        $(document).on("click", ".buddybot-chatbot-delete", function() {  
			assistantId = $(this).attr("assistant-id");
            chatbotId = $(this).attr("chatbot-id");
        });

        $("#buddybot-confirm-del-btn").on("click", function() {
            $("#buddybot-cancel-del-btn").prop("disabled", true);
            $("#buddybot-confirm-del-btn").prop("disabled", true);
            $("#buddybot-del-msg").show();
            deleteBuddyBot();
        });

        function deleteBuddyBot(){

            const data = {
                "action": "deleteBuddyBot",
                "assistant_id": assistantId,
                "chatbot_id": chatbotId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    showToast("success", response.message);
                    $("a.buddybot-chatbot-delete[chatbot-id=" + chatbotId + "]").closest("tr").remove();
                } else {
                    showAlert(response.message);
                }
                $(".buddybot-modal.show").removeClass("show");
                $("#buddybot-cancel-del-btn").prop("disabled", false);
                $("#buddybot-confirm-del-btn").prop("disabled", false);
                $("#buddybot-del-msg").hide();
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

}