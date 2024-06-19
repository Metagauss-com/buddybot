<?php
namespace BuddyBot\Frontend\Requests;

class BuddybotChat extends \BuddyBot\Frontend\Requests\Moroot
{
    protected function shortcodeJs()
    {
        $this->toggleAlertJs();
        $this->onLoadJs();
        $this->getUserThreadsJs();
        $this->startNewThreadBtnJs();
        $this->singleThreadBackBtnJs();
        $this->threadListItemJs();
        $this->getThreadInfoJs();
        $this->loadThreadListViewJs();
        $this->loadSingleThreadViewJs();
        $this->getMessagesJs();
    }

    private function toggleAlertJs()
    {
        echo '
        function showAlert(type = "danger", text = "") {
            let alert = $(".buddybot-chat-conversation-alert[data-bb-alert=" + type + "]");
            alert.text(text);
            alert.removeClass("visually-hidden");
        }

        function hideAlerts() {
            let alert = $(".buddybot-chat-conversation-alert");
            alert.addClass("visually-hidden");
        }
        ';
    }

    private function onLoadJs()
    {
        echo '
            loadThreadListView();
        ';
    }

    private function getUserThreadsJs()
    {
        echo '
        function getUserThreads() {

            const data = {
                "action": "getConversationList"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#buddybot-chat-conversation-list-loader").addClass("visually-hidden");
                $("#buddybot-chat-conversation-list-wrapper").html(response);
            });
        }
        ';
    }

    private function startNewThreadBtnJs()
    {
        echo '
        $("#buddybot-chat-conversation-start-new").click(function(){
            loadSingleThreadView();
        });
        ';
    }

    private function singleThreadBackBtnJs()
    {
        echo '
        $("#buddybot-single-conversation-back-btn").click(function(){
            loadThreadListView();
        });
        ';
    }

    private function threadListItemJs()
    {
        echo '
        $("#buddybot-chat-conversation-list-wrapper").on("click", "li", function(){
            let threadId = $(this).attr("data-bb-threadid");
            loadSingleThreadView(threadId);
        });';
    }

    private function getThreadInfoJs()
    {
        echo '
        function getThreadInfo(threadId = "") {

            if (threadId === "") {
                return;
            }

            const data = {
                "action": "getThreadInfo",
                "thread_id": threadId,
                "nonce": "' . wp_create_nonce('get_thread_info') . '"
            };

            $.post(ajaxurl, data, function(response) {
                alert(response);
            });
        }
        ';
    }

    private function loadThreadListViewJs()
    {
        echo '
        function loadThreadListView() {
            hideAlerts();
            getUserThreads();
            $("#buddybot-chat-conversation-list-header").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-loader").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").removeClass("visually-hidden");
            $("#buddybot-single-conversation-wrapper").addClass("visually-hidden");
            sessionStorage.removeItem("bbCurrentThreadId");
            $("#buddybot-single-conversation-messages-wrapper").html("");
        }';
    }

    private function loadSingleThreadViewJs()
    {
        echo '
        function loadSingleThreadView(threadId = false) {
            hideAlerts();
            $("#buddybot-chat-conversation-list-header").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").html("");
            $("#buddybot-single-conversation-wrapper").removeClass("visually-hidden");

            if (threadId === false) {
                sessionStorage.removeItem("bbCurrentThreadId");
            } else {
                sessionStorage.setItem("bbCurrentThreadId", threadId);
                getMessages(limit = 10);
            }
        }';
    }

    private function getMessagesJs()
    {
        echo '
        function getMessages(limit = 10) {
            const data = {
                "action": "getMessages",
                "thread_id": sessionStorage.getItem("bbCurrentThreadId"),
                "limit": limit,
                "order": "desc",
                "nonce": "' . wp_create_nonce('get_messages') . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    $("#buddybot-single-conversation-messages-wrapper").append(response.html);
                } else {
                    showAlert("danger", response.message);
                }
                
            });
        }';
    }
}