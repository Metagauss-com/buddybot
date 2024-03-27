<?php

namespace MetagaussOpenAI\Admin\Requests;

final class Playground extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->setVarsJs();
        $this->disableMessageJs();
        $this->updateStatusJs();
        $this->getAssistantsJs();
        $this->sendMessageBtnJs();
        $this->createThreadJs();
        $this->createMessageJs();
        $this->createRunJs();
        $this->retrieveRunJs();
        $this->listMessagesJs();
        $this->storeThreadInfoJs();
        $this->scrollToMessageJs();
        $this->selectThreadJs();
        $this->pastMessagesJs();
    }

    private function setVarsJs()
    {
        echo '
        let checkRun = "";
        const gettingAssistants = "' . esc_html('Getting list of assistants.', 'metagauss-openai') . '";
        const assistantsUpdated = "' . esc_html('Assistants updated.', 'metagauss-openai') . '";
        const creatingThread = "' . esc_html('Starting new conversation.', 'metagauss-openai') . '";
        const threadCreated = "' . esc_html('Conversation started.', 'metagauss-openai') . '";
        const sendingMessage = "' . esc_html('Sending message to the Assistant.', 'metagauss-openai') . '";
        const messageSent = "' . esc_html('Message sent.', 'metagauss-openai') . '";
        const creatingRun = "' . esc_html('Asking assistant to read your message.', 'metagauss-openai') . '";
        const runCreated = "' . esc_html('Assistant is reading your message.', 'metagauss-openai') . '";
        const retrievingRun = "' . esc_html('Assistant is writing response to your message.', 'metagauss-openai') . '";
        const gettingResponse = "' . esc_html("Fetching Assistant response.", 'metagauss-openai') . '";
        const responseUpdated = "' . esc_html("Assistant response received.", 'metagauss-openai') . '";
        const gettingPastMessages = "' . esc_html("Loading previous messages.", 'metagauss-openai') . '";
        const pastMessagesUpdated = "' . esc_html("Loaded previous messages.", 'metagauss-openai') . '";
        ';
    }

    private function disableMessageJs()
    {
        echo '
        function disableMessage(propState = true) {
            $("#mgao-playground-send-message-btn").prop("disabled", propState);
            $("#mgao-playground-new-message-text").prop("disabled", propState);
        }
        ';
    }

    private function updateStatusJs()
    {
        echo '
        function updateStatus(text) {
            $("#mgoa-playground-message-status").children("span").html(text);
        }
        ';
    }

    private function getAssistantsJs()
    {
        $nonce = wp_create_nonce('get_assistants');
        echo '
        getAssistants();
        function getAssistants() {

            disableMessage();
            updateStatus(gettingAssistants);

            const data = {
                "action": "getAssistantOptions",
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    $("#mgoa-playground-assistants-list").html(response.html);
                    updateStatus(assistantsUpdated);
                    disableMessage(false);
                } else {
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function sendMessageBtnJs()
    {
        echo '
        $("#mgao-playground-send-message-btn").click(sendMessage);
        function sendMessage() {
            disableMessage();
            const threadId = $("#mgao-playground-thread-id-input").val();
            if (threadId === "") {
                createThread();
            } else {
                addMessage();
            }
        }
        ';
    }

    private function createThreadJs()
    {
        $nonce = wp_create_nonce('create_thread');
        echo '
        function createThread() {
            
            disableMessage();
            updateStatus(creatingThread);
            
            const data = {
                "action": "createThread",
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(threadCreated);
                    $("#mgao-playground-thread-id-input").val(response.result.id);
                    addMessage();
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function createMessageJs()
    {
        $nonce = wp_create_nonce('create_message');
        echo '
        function addMessage() {

            disableMessage();
            updateStatus(sendingMessage);

            const threadId = $("#mgao-playground-thread-id-input").val();
            const message = $("#mgao-playground-new-message-text").val();

            const data = {
                "action": "createMessage",
                "thread_id": threadId,
                "message": message,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(messageSent);
                    $("#mgao-playground-new-message-text").val("");
                    $("#mgoa-playground-messages-list").append(response.html);
                    scrollToMessage(response.result.id);
                    createRun();
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function createRunJs()
    {
        $nonce = wp_create_nonce('create_run');
        echo '
        function createRun() {

            disableMessage();
            updateStatus(creatingRun);

            const threadId = $("#mgao-playground-thread-id-input").val();
            const assistantId = $("#mgoa-playground-assistants-list").val();

            const data = {
                "action": "createRun",
                "thread_id": threadId,
                "assistant_id": assistantId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(runCreated);
                    $("#mgao-playground-run-id-input").val(response.result.id);
                    checkRun = setInterval(retrieveRun, 2000);
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function retrieveRunJs()
    {
        $nonce = wp_create_nonce('retrieve_run');
        echo '
        function retrieveRun() {

            disableMessage();
            updateStatus(retrievingRun);

            const threadId = $("#mgao-playground-thread-id-input").val();
            const runId = $("#mgao-playground-run-id-input").val();

            const data = {
                "action": "retrieveRun",
                "thread_id": threadId,
                "run_id": runId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    if (response.result.status == "completed") {
                        clearInterval(checkRun);
                        listMessages(1);
                        scrollToMessage(response.result.last_id);
                    }
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function listMessagesJs()
    {
        $nonce = wp_create_nonce('list_messages');
        echo '
        function listMessages(limit = 1, order = "desc") {

            disableMessage();
            updateStatus(gettingResponse);

            const threadId = $("#mgao-playground-thread-id-input").val();

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "limit": limit,
                "order": order,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                
                response = JSON.parse(response);

                if (response.success) {
                    updateStatus(responseUpdated);
                    $("#mgoa-playground-messages-list").append(response.html);
                    storeThreadInfo(response.result);
                    disableMessage(false);
                } else {
                    disableMessage(false);
                    updateStatus(response.message);
                }
            });
        }
        ';
    }

    private function storeThreadInfoJs()
    {
        echo '
        function storeThreadInfo(thread)
        {
            $("#mgoa-playground-first-message-id").val(thread.first_id);
            $("#mgoa-playground-last-message-id").val(thread.last_id);
            $("#mgoa-playground-has-more-messages").val(thread.has_more);

            if (thread.has_more) {
                $("#mgoa-playground-past-messages-btn").css("opacity", 100);
            } else {
                $("#mgoa-playground-past-messages-btn").css("opacity", 0);
            }
        }
        ';
    }

    private function scrollToMessageJs()
    {
        echo '
        function scrollToMessage(messageId) {
            $("#mgoa-playground-messages-list").animate({
                scrollTop: $("#" + messageId).offset().top
            }, 1000);
        }
        ';
    }

    private function selectThreadJs()
    {
        echo '
        $("#mgoa-playground-threads-list").on("click", ".mgoa-playground-threads-list-item", function() {
            
            const threadId = $(this).attr("data-mgoa-threadid");
            const highlightClass = "fw-bold text-primary";
            
            $("#mgoa-playground-messages-list").html("");
            $("#mgao-playground-thread-id-input").val(threadId);
            $(".mgoa-playground-threads-list-item.fw-bold.text-primary").removeClass(highlightClass);
            $(this).addClass(highlightClass);
            
            listMessages(5);
        });
        ';
    }

    private function pastMessagesJs()
    {
        $nonce = wp_create_nonce('list_messages');

        echo '
        $("#mgoa-playground-past-messages-btn").click(function(){

            updateStatus(gettingPastMessages);

            const hasMore = $("#mgoa-playground-has-more-messages").val();

            if (hasMore == false) {
                return;
            }

            const firstId = $("#mgoa-playground-first-message-id").val();
            const lastId = $("#mgoa-playground-last-message-id").val();
            const threadId = $("#mgao-playground-thread-id-input").val();

            const data = {
                "action": "listMessages",
                "thread_id": threadId,
                "limit": 50,
                "after": lastId,
                "order": "desc",
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    updateStatus(pastMessagesUpdated);
                    $("#mgoa-playground-messages-list").prepend(response.html);
                    storeThreadInfo(response.result);
                } else {
                    updateStatus(response.message);
                }
            });
          });
        ';
    }
}