jQuery( document ).ready(
    function(){
        var dismissed = bb_ajax_object.bb_dismissed_modal;
        if (dismissed) {
            return;
        }
        jQuery('#buddybot-welcome-modal').modal('show');

        jQuery(".bb-get-started").click(function() {
            DismissModal(true);
        });

        jQuery(".bb-dismiss-modal").click(function() {
            DismissModal(false);
        });
 
        function DismissModal(redirect = false) {

            var notice_name = 'buddybot_welcome_modal_dismissed';
            const data = {'action': 'bb_dismissible_notice','notice_name': notice_name,'nonce':bb_ajax_object.nonce};
            jQuery.post(
                bb_ajax_object.ajax_url,
                data,
                function(response) {
                    if (redirect) {
                        location.replace("admin.php?page=buddybot-settings");
                    }

                }
            );

        }
 
	}
);