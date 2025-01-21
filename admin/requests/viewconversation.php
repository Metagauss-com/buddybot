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
        $this->listConversationJs();
        $this->getRelatedConversationMsg();
        $this->deleteConversationJs();
    }

    private function getRelatedConversationMsg()
    {
        $nonce = wp_create_nonce('get_related_conversation_msg');
        echo '
        getRelatedConversationMsg();
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
                    $("#buddybot-conversation-loading-spinner").addClass("visually-hidden");
                    $("#buddybot-related-conversation-msg").text(response.message);
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
                    var cleanedHtml = response.html.replace(/【.*?†.*?】/g, "");
                    $("#buddybot-playground-messages-list").append(cleanedHtml);
                    //storeThreadInfo(response.result);
                } else {
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
            $("#buddybot-conversation-delete-btn").prop("disabled", true);
            $("#buddybot-delete-viewconversation-modal").modal("show");
        });

        $("#buddybot-delete-viewconversation-cancel-btn").click(function(){
            $("#buddybot-conversation-delete-btn").prop("disabled", false);
        });

        $("#buddybot-confirm-viewconversation-delete-btn").click(function(){
            $("#buddybot-delete-viewconversation-modal").modal("hide");

            const data = {
                "action": "deleteConversation",
                "thread_id": "' . esc_js($this->thread_id) . '",
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    
                } else {
                    showAlert(response.message);
                }

                disableMessage(false);
                toggleThreadBtns();
            });
        });

        ';
    }

    private function pastConversationJs()
    {
        $nonce = wp_create_nonce('list_conversation');

        echo '
        $("#buddybot-playground-past-messages-btn").click(function(){

            updateStatus(gettingPastMessages);
            disableMessage(true);
            $("#buddybot-playground-past-messages-btn").children("span").addClass("buddybot-rotate-icon");

            const hasMore = $("#buddybot-playground-has-more-messages").val();

            if (hasMore == false) {
                return;
            }

            const firstId = $("#buddybot-playground-first-message-id").val();
            const lastId = $("#buddybot-playground-last-message-id").val();
            const threadId = $("#mgao-playground-thread-id-input").val();

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "limit": 5,
                "after": lastId,
                "order": "desc",
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);
                
                if (response.success) {
                    updateStatus(pastMessagesUpdated);
                    var cleanedHtml = response.html.replace(/【.*?†.*?】/g, "");
                    $("#buddybot-playground-messages-list").prepend(cleanedHtml);
                    storeThreadInfo(response.result);
                    scrollToTop();
                } else {
                    updateStatus(response.message);
                }

                $("#buddybot-playground-past-messages-btn").children("span").removeClass("buddybot-rotate-icon");
                disableMessage(false);
                toggleThreadBtns();
            });
          });
        ';
    }
}