<?php

namespace BuddyBot\Admin\Requests;

final class Conversations extends \BuddyBot\Admin\Requests\MoRoot
{

    public function requestJs()
    {
        $this->paginationDropdownJs();
        $this->deleteConversation();
    }

    private function paginationDropdownJs()
    {
        $nonce = wp_create_nonce('pagination_dropdown');
        echo '
        $("#buddybot-conversation-pagination").on("change", function() {
			var selectedValue = $(this).val();

			const data = {
				"action": "saveConversationLimit",
				"selected_value": selectedValue,
				"nonce": "' . esc_js($nonce) . '"
			};

			$.post(ajaxurl, data, function(response) {
				response = JSON.parse(response);

				if (response.success) {
					location.reload();
				}
			});

		});
        ';

    }

    private function deleteConversation()
    {
        $nonce = wp_create_nonce('delete_conversation');
        echo'

        let threadId;
        $(document).on("click", ".buddybot-conversation-delete", function() { 
            hideAlert();
			threadId = $(this).attr("thread-id");
        });

        $("#buddybot-confirm-del-conversation-btn").click(function(){
            $("#buddybot-cancel-del-conversation-btn").prop("disabled", true);
            $("#buddybot-confirm-del-conversation-btn").prop("disabled", true);
            $("#buddybot-del-msg").show();
            deleteConversation();
        });

        function deleteConversation(){

            const data = {
                "action": "deleteConversation",
                "thread_id": threadId,
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    location.reload();
                } else {
                    showAlert(response.message);
                }
                $(".buddybot-modal.show").removeClass("show");
                $("#buddybot-cancel-del-conversation-btn").prop("disabled", false);
                $("#buddybot-confirm-del-conversation-btn").prop("disabled", false);
                $("#buddybot-del-msg").hide();
            });
        }
        ';
    }

}