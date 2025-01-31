jQuery(document).ready(function($){

    function verifyDeepSeekApiKey(apiKey) {
        const data = {
            "action": "verifyDeepSeekApiKey",
            "api_key": apiKey,
            "nonce": deepseek_api_key_nonce
        };

        $.post(ajaxurl, data, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                saveDeepSeekApiKey(apiKey);
            } else {
                dataErrors.push(response.message);
                displayErrors();
                hideWordpressLoader("#buddybot-settings-update-btn");
            }
        });
    }

    function saveDeepSeekApiKey(apiKey) {
        const section = $("#mgao-settings-section-select").val();
        optionsData["deepseek_api_key"] = apiKey;

        const data = {
            "action": "saveSettings",
            "options_data": JSON.stringify(optionsData),
            "section": section,
            "nonce": save_settings_nonce
        };

        $.post(ajaxurl, data, function(response) {
            response = JSON.parse(response);
            if (response.success) {
                location.replace(admin_url + "admin.php?page=buddybot-settings&section=" + section + "&success=1");
            } else {
                $("#buddybot-settings-error-message").html(response.message);
                dataErrors = response.errors;
                displayErrors();
            }

            disableFields(false);
            hideWordpressLoader("#buddybot-settings-update-btn");
        });
    }

    function getDeepSeekApiKey() {
        let key = $("#buddybot-settings-deepseek-api-key").val();
        key = $.trim(key);

        if (key === "") {
            dataErrors.push("DeepSeek API Key cannot be empty.");
            displayErrors();
            hideWordpressLoader("#buddybot-settings-update-btn");
        } else {
            verifyDeepSeekApiKey(key);
        }
    }

    $("#buddybot-settings-update-btn").click(function() {
        showWordpressLoader("#buddybot-settings-update-btn");
        getDeepSeekApiKey();
    });

});
