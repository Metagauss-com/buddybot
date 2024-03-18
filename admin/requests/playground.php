<?php

namespace MetagaussOpenAI\Admin\Requests;

final class Playground extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->getAssistantsJs();
        $this->sendMessageBtnJs();
    }

    private function getAssistantsJs()
    {
        $nonce = wp_create_nonce('get_assistants');
        echo '
        getAssistants();
        function getAssistants() {

            const data = {
                "action": "getAssistantOptions",
                "nonce": "' . $nonce . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    $("#mgoa-playground-assistants-list").html(response.html);
                } else {

                }
            });
        }
        ';
    }

    private function sendMessageBtnJs()
    {
        echo '
        function sendMessage() {
            const threadId = $("#mo-playground-new-message-text").attr("data-mo-threadid");
            if (threadId === "") {
                createThread();
            } else {
                addMessage();
            }
        }
        ';
    }
}