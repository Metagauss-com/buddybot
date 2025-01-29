(function ($) {
    'use strict';

    //$(document).ready(function () {

        //Range Value of Temperature
        $('#buddybot-editassistant-assistanttemperature-range').on('input', function () {
            var value = $(this).val();
            $('#buddybot-editassistant-assistanttemperature-value').text(value);
            
        });

        var initialValue = $('#buddybot-editassistant-assistanttemperature-range').val();
        $('#buddybot-editassistant-assistanttemperature-value').text(initialValue);
        

        //Range Value of Top_p
        $('#buddybot-editassistant-assistanttopp-range').on('input', function() {
            var value = $(this).val();
            $('#buddybot-editassistant-assistanttopp-value').text(value);    
        });

        var initialValue = $('#buddybot-editassistant-assistanttopp-range').val();
        $('#buddybot-editassistant-assistanttopp-value').text(initialValue);

        // Function to handle DeepSeek model selection
        function handleDeepSeekModelSelection() {
            const select = $("#buddybot-editassistant-assistantmodel");
            const data = {
                "action": "getDeepSeekModels",
                "nonce": deepseek_models_nonce
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                    select.html(response.html);
                    select.siblings(".buddybot-dataload-spinner").hide();
                } else {
                    if(response.empty_key) {
                        select.html(response.html);
                    }
                    select.siblings(".buddybot-dataload-spinner").hide();
                    showAlert(response.message);
                }
            });
        }

        // Update createAssistant function to handle DeepSeek model
        function createAssistant(){
            hideAlert();
            disableFields(true);
            showBtnLoader("#buddybot-editassistant-editassistant-submit");
            let aData = assistantData();
            let vectorStoreId = vectorstore_id;

            const data = {
                "action": "createDeepSeekAssistant",
                "assistant_id": assistant_id,
                "assistant_data": JSON.stringify(aData),
                "vectorstore_id": vectorStoreId,
                "nonce": create_deepseek_assistant_nonce
            };

            $.post(ajaxurl, data, function(response) {
                hideBtnLoader("#buddybot-editassistant-editassistant-submit");
                response = JSON.parse(response);
                if (response.success) {
                    location.replace(admin_url + "admin.php?page=buddybot-editassistant&assistant_id=" + response.result.id);
                } else {
                    showAlert(response.message);
                }
                disableFields(false);
            });
        }

   // });

})(jQuery);
