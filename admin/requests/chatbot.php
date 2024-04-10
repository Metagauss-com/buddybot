<?php

namespace MetagaussOpenAI\Admin\Requests;

class ChatBot extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->selectAssistantModalJs();
    }

    protected function selectAssistantModalJs()
    {
        $this->getAssistantsListJs();
        $this->selectAssistantJs();
    }

    protected function getAssistantsListJs()
    {
        echo '
        const selectAssistantModal = document.getElementById("mgoa-select-assistant-modal");
        if (selectAssistantModal) {
            selectAssistantModal.addEventListener("show.bs.modal", event => {
                
                const data = {
                    "action": "selectAssistantModal",
                    "nonce": "' . wp_create_nonce('select_assistant_modal') . '"
                };
  
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        $("#mgao-select-assistant-modal-list").append(response.html);
                    }
                });
            });
        }
        ';
    }

    protected function selectAssistantJs()
    {
        echo '
        $("#mgao-select-assistant-modal-list").on("click", ".list-group-item", function(){
            
            let assistantId = $(this).attr("data-mgao-id");
            let assistantName = $(this).attr("data-mgao-name");
            
            $("#mgao-chatbot-assistant-name").html(assistantName);
            $("#mgao-chatbot-assistant-id").val(assistantId);
        });
        ';
    }
}