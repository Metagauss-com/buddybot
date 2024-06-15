<?php
namespace BuddyBot\Frontend\Requests;

class BuddybotChat extends \BuddyBot\Frontend\Requests\Moroot
{
    protected function shortcodeJs()
    {
        $this->onLoadJs();
        $this->getUserThreadsJs();
        $this->singleThreadBackBtnJs();
        $this->threadListItemJs();
        $this->getThreadInfoJs();
        $this->loadThreadListViewJs();
        $this->loadSingleThreadViewJs();
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
            getUserThreads();
            $("#buddybot-chat-conversation-list-header").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-loader").removeClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").removeClass("visually-hidden");
            $("#buddybot-single-conversation-wrapper").addClass("visually-hidden");
            sessionStorage.removeItem("bbCurrentThreadId");
        }';
    }

    private function loadSingleThreadViewJs()
    {
        echo '
        function loadSingleThreadView(threadId = false) {
            $("#buddybot-chat-conversation-list-header").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").addClass("visually-hidden");
            $("#buddybot-chat-conversation-list-wrapper").html("");
            $("#buddybot-single-conversation-wrapper").removeClass("visually-hidden");

            if (threadId === false) {
                sessionStorage.removeItem("bbCurrentThreadId");
            } else {
                sessionStorage.setItem("bbCurrentThreadId", threadId);
            }
        }';
    }
}