jQuery( function( $ ) {
    var buddybot_plugin_deactivate_location = '';

    function deactivationAlertShow(message) {
        $( '#buddybot-plugin-deactivation-alert' ).text(message);
        $( '#buddybot-plugin-deactivation-alert' ).show();
    }

    function deactivationAlertHide() {
        $( '#buddybot-plugin-deactivation-alert' ).text('');
        $( '#buddybot-plugin-deactivation-alert' ).hide();
    }

    // show feedback modal on click on the deactivate link
    $( '#the-list' ).find('[data-slug="buddybot-ai-custom-ai-assistant-and-chat-agent"] span.deactivate a').click( function(event) {
        $("#buddybot-deactivation-feedback").addClass("show");
        buddybot_plugin_deactivate_location = $(this).attr('href');
        event.preventDefault();
    });

    // skip and deactivation
    $( document ).on( 'click', '#buddybot-plugin-feedback-direct-deactivation', function() {
        deactivationAlertShow(buddybot_feedback.deactivation);
        setTimeout( function() {
            location.href = buddybot_plugin_deactivate_location;
        }, 1000 );
    });

    $( document ).on( 'click', '#buddybot-plugin-feedback-deactivation', function() {
        
        let feedback = $("#buddybot-feedback-message").val();
        let tempDeactivate = $("#buddybot-temp-deactivate").prop("checked") ? 1 : 0;

        if (feedback === "" && !tempDeactivate) {
            deactivationAlertShow(buddybot_feedback.empty);
            return;
        }
        
        deactivationAlertShow(buddybot_feedback.deactivation);
        
        const data = {
            "action": "buddybotSendPluginFeedback",
            "feedback_message": feedback,
            "temp_deactivate": tempDeactivate,
            "nonce" : buddybot_feedback.nonce
        };

        $.post(buddybot_feedback.ajaxurl, data, function(response) {
            response = JSON.parse(response);

            if (response.success) {
                location.href = buddybot_plugin_deactivate_location;
            } else {
                deactivationAlertShow(response.message);
            }
        });

    });

});