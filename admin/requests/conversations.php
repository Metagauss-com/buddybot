<?php

namespace BuddyBot\Admin\Requests;

final class Conversations extends \BuddyBot\Admin\Requests\MoRoot
{

    public function requestJs()
    {
        $this->getUserJs();
        $this->saveConversationLimitPerPageJs();
        $this->loadMoreConversationsJs();
        $this->deleteConversation();
        $this->renumberRowsJs();
    }

    protected function userId() {
        if (!empty($_GET['user_id'])) {
            return sanitize_text_field($_GET['user_id']);
        } else {
            return 0;
        }
    }

    private function getUserJs()
    {
        $nonce = wp_create_nonce('get_conversations');
        echo '
        getConversations();
        function getConversations(paged = 1) {
        const user_id = ' . $this->userId() . ';
			
			const data = {
				"action": "getConversations",
				"paged": paged,
                "user_id": user_id,
				"nonce": "' . esc_js($nonce) . '"
			};

			$.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

				if (response.success) {
					$("#buddybot-assistants-loading-spinner").addClass("visually-hidden");
					$(".buddybot-org-conversations-table tbody").append(response.html);

					if (response.has_more) {
						$("#buddybot-conversations-load-more-btn").removeClass("visually-hidden");
						$("#buddybot-conversations-load-more-btn").prop("disabled", false);
						$("#buddybot-conversations-load-more-btn").find(".buddybot-loaderbtn-label").removeClass("visually-hidden");
						$("#buddybot-conversations-load-more-btn").find(".buddybot-loaderbtn-spinner").addClass("visually-hidden");
					} else {
						$("#buddybot-conversations-load-more-btn").addClass("visually-hidden");
						$("#buddybot-conversations-no-more").removeClass("visually-hidden");
						
					}

				} else {
					showAlert(response.message);
				}
			});
		}
        ';
    }

    private function saveConversationLimitPerPageJs()
    {
        $nonce = wp_create_nonce('save_conversation_limit_per_page');
        echo '
        $("#buddybot-conversation-load-more-limit").on("change", function() {
			var selectedValue = $(this).val();

			const data = {
				"action": "saveConversationLimitPerPage",
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

    private function loadMoreConversationsJs()
    {
        echo '
        $("#buddybot-conversations-load-more-btn").click(function() {
            let lastConversation = $("tr.buddybot-conversations-table-row:last-child").attr("data-buddybot-pageid");
            $(this).prop("disabled", true);
            $(this).find(".buddybot-loaderbtn-label").addClass("visually-hidden");
            $(this).find(".buddybot-loaderbtn-spinner").removeClass("visually-hidden");
            getConversations(lastConversation);
        });
        ';
    }

    private function deleteConversation()
    {
        $nonce = wp_create_nonce('delete_conversation');
        echo'

        let threadId;
        $(".buddybot-org-conversations-table").on("click", ".buddybot-conversation-delete", function(){
			threadId = $(this).attr("data-buddybot-itemid");
				
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
                    $("#buddybot-delete-conversation-modal").modal("hide");
                    $("#buddybot-delete-conversation-cancel-btn").prop("disabled", false);
                    $("#buddybot-confirm-conversation-delete-btn").prop("disabled", false);
                    $("#buddybot-deleting-conversation-msg").hide();
					$(".buddybot-conversation-delete").prop("disabled", false);
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
            console.log("renumbering");
			$("tr.buddybot-conversations-table-row").each(function() {
				$(this).children("th.buddybot-conversations-sr-no").html(i);
				i++;
			});
		}
        ';
    }

}