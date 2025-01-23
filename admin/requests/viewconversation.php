<?php

namespace BuddyBot\Admin\Requests;

final class ViewConversation extends \BuddyBot\Admin\Requests\MoRoot
{
    protected $thread_id = '';
    protected $user_id = '';

    protected function threadId()
    {
        if (!empty($_GET['thread_id'])) {
            $this->thread_id = sanitize_text_field($_GET['thread_id']);
        }

        if (!empty($_GET['user_id'])) {
            $this->user_id = sanitize_text_field($_GET['user_id']);
        }
    }

    public function requestJs()
    {
        $this->threadId();
        $this->storeThreadInfoJs();
        $this->listConversationJs();
        $this->getRelatedConversationMsg();
        $this->deleteConversationJs();
        $this->pastConversationJs();
        $this->scrollToMessageJs();
        $this->togglePastConversationBtnJs();
    }

    private function getRelatedConversationMsg()
    {
        $nonce = wp_create_nonce('get_related_conversation_msg');
        echo '
        function getRelatedConversationMsg() {
            const data = {
                "action": "getRelatedConversationMsg",
                "thread_id": "' . esc_js($this->thread_id) . '",
                "user_id": "' . esc_js($this->user_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    if (response.disabled) {
                        $("#buddybot-related-conversation-msg-container").addClass("notice notice-warning").removeClass("notice-success");
                        $("#buddybot-related-conversation-msg").text(response.message);
                    } else {
                        $("#buddybot-related-conversation-msg-container").addClass("notice notice-success").removeClass("notice-warning");
                        $("#buddybot-related-conversation-msg").html(response.message);
                    }   
                } else {
                    showAlert(response.message);
                }
            });
        }
        ';
    }

    private function listConversationJs()
    {
        $nonce = wp_create_nonce('list_conversation');
        echo '
        listConversation();
        function listConversation() {

            const data = {
                "action": "listConversation",
                "thread_id": "' . esc_js($this->thread_id) . '",
                "limit": 5,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);

                if (response.success) {
                    $("#buddybot-conversation-loading-spinner").addClass("visually-hidden");
                    getRelatedConversationMsg();
                    var cleanedHtml = response.html.replace(/【.*?†.*?】/g, "");
                    $("#buddybot-conversation-messages-list").append(cleanedHtml);
                    storeThreadInfo(response.result);
                    scrollToBottom(response.result.first_id);
                } else {
                    $("#buddybot-conversation-loading-spinner").addClass("visually-hidden");
                    showAlert(response.message);
                }

                //disableMessage(false);
                //toggleThreadBtns();
            });
        }
        ';
    }

    private function deleteConversationJs()
    {
        $nonce = wp_create_nonce('delete_conversation');
        echo '
        $("#buddybot-conversation-delete-btn").click(function(){
            hideAlert();
            $("#buddybot-conversation-delete-btn").prop("disabled", true);
            $("#buddybot-delete-viewconversation-modal").modal("show");
        });

        $("#buddybot-delete-viewconversation-cancel-btn").click(function(){
            $("#buddybot-conversation-delete-btn").prop("disabled", false);
        });

        $("#buddybot-confirm-viewconversation-delete-btn").click(function(){
            $("#buddybot-delete-viewconversation-cancel-btn").prop("disabled", true);
            $("#buddybot-confirm-viewconversation-delete-btn").prop("disabled", true);
            $("#buddybot-deleting-viewconversation-msg").show();

            const data = {
                "action": "deleteConversation",
                "thread_id": "' . esc_js($this->thread_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    window.location.href = "admin.php?page=buddybot-conversations";
                } else {
                    showAlert(response.message);
                }
                $("#buddybot-delete-viewconversation-modal").modal("hide");
                $("#buddybot-delete-viewconversation-cancel-btn").prop("disabled", false);
                $("#buddybot-confirm-viewconversation-delete-btn").prop("disabled", false);
                $("#buddybot-deleting-viewconversation-msg").hide();
                $("#buddybot-conversation-delete-btn").prop("disabled", false);
            });
        });

        ';
    }

    private function storeThreadInfoJs()
    {
        echo '
        function storeThreadInfo(thread)
        {
            $("#buddybot-conversation-first-message-id").val(thread.first_id);
            $("#buddybot-conversation-last-message-id").val(thread.last_id);
            $("#buddybot-conversation-has-more-messages").val(thread.has_more);
        }
        ';
    }

    private function pastConversationJs()
    {
        $nonce = wp_create_nonce('list_conversation');

        echo '
        $("#buddybot-conversation-past-messages-btn").click(function(){

            hideAlert();

            const hasMore = $("#buddybot-conversation-has-more-messages").val();

            if (hasMore == false) {
                return;
            }

            const firstId = $("#buddybot-conversation-first-message-id").val();
            const lastId = $("#buddybot-conversation-last-message-id").val();

            const data = {
                "action": "listConversation",
                "thread_id": "' . esc_js($this->thread_id) . '",
                "limit": 5,
                "after": lastId,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);
                
                if (response.success) {
                    //updateStatus(pastMessagesUpdated);
                    var cleanedHtml = response.html.replace(/【.*?†.*?】/g, "");
                    $("#buddybot-conversation-messages-list").prepend(cleanedHtml);
                    storeThreadInfo(response.result);
                    scrollToTop();
                } else {
                    showAlert(response.message);
                }

                //$("#buddybot-playground-past-messages-btn").children("span").removeClass("buddybot-rotate-icon");
                //disableMessage(false);
                togglePastConversationBtn();
            });
          });
        ';
    }

    private function scrollToMessageJs()
    {
        echo '
        function scrollToBottom(id) {
            let messageList = $("#buddybot-conversation-messages-list");
            
            messageList.animate({
                scrollTop: messageList[0].scrollHeight // Scroll to the bottom of the message list
            }, 1000); // Duration of the scroll
        }

        function scrollToTop() {
            $("#buddybot-conversation-messages-list").animate({
                scrollTop: 0
            }, 1000);
        }
        ';
    }

    private function togglePastConversationBtnJs()
    {
        echo '
        function togglePastConversationBtn() {
            let hasMore = $("#buddybot-conversation-has-more-messages").val();
            if (hasMore === "true") {
                $("#buddybot-conversation-past-messages-btn").css("opacity", 100);
            } else {
                $("#buddybot-conversation-past-messages-btn").css("opacity", 0);
            }
        }
        ';
    }
}