<?php

namespace MetagaussOpenAI\Admin\Requests;

class ChatBot extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->sendBtnJs();
        $this->createThreadJs();
        $this->runAssistantJs();
        $this->runStatusJs();
        $this->fetchResponseJs();
    }

    private function sendBtnJs()
    {
        $nonce = wp_create_nonce('add_user_message');

        echo '
        $("#mo-send-btn").click(function(){
            
            let threadId = $("#thread-id").val();
            userMessage = $("#user-message").val();

            const data = {
                "action": "addMessage",
                "message": userMessage,
                "thread_id": threadId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                if (response !== false) {
                    runAssistant();
                }
            });
            
        });
        ';
    }

    private function createThreadJs()
    {
        $nonce = wp_create_nonce('create_thread');

        echo '
        createThread();
        function createThread() {
            let threadId = $("#thread-id").val();
            
            if (threadId !== "") {
                return;
            }

            const data = {
                "action": "createThread",
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                threadId = response.id;
                $("#thread-id").val(threadId);
                $("#thread-id-visible").text(threadId);
            });
        }
        ';
    }
    
    private function runAssistantJs()
    {
        $nonce = wp_create_nonce('run_assistant');

        echo '
        function runAssistant() {
            
            let threadId = $("#thread-id").val();

            const data = {
                "action": "runAssistant",
                "thread_id": threadId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                $("#run-id").val(response.id);
                $("#run-id-visible").text(response.id);
                const runTimeout = setTimeout(runStatus, 5000);
            });
        }
        ';
    }

    private function runStatusJs()
    {
        $nonce = wp_create_nonce('run_status');

        echo '
        function runStatus() {
            
            let threadId = $("#thread-id").val();
            let runId = $("#run-id").val();

            const data = {
                "action": "runStatus",
                "thread_id": threadId,
                "run_id": runId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.status === "completed") {
                    fetchResponse();
                } else {
                    alert("run failed");
                }
            });
        }
        ';
    }

    private function fetchResponseJs()
    {
        $nonce = wp_create_nonce('fetch_response');

        echo '
        function fetchResponse() {
            
            let threadId = $("#thread-id").val();

            const data = {
                "action": "fetchResponse",
                "thread_id": threadId,
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {

                response = JSON.parse(response);
                const messages = response.data;

                $("#conversation").html("");
                
                $.each(messages, function(index, value){
                    let message = messages[index].content[0].text.value;
                    if (messages[index].role === "assistant") {
                        $("#conversation").append("<div><strong>OpenAI </strong>" + message + "</div>");
                    } else {
                        $("#conversation").append("<div><strong>You </strong>" + message + "</div>");
                    }
                });

            });
        }
        ';
    }
}