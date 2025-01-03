jQuery( document ).ready(
    function(){
        var dismissed = bb_ajax_object.bb_dismissed_modal;
        if (dismissed) {
            return;
        }
            jQuery('#buddybot-welcome-modal').modal('show');

		jQuery( ".buddybot-dismiss-welcome-modal" ).click(
            function()
            {
 
                    var notice_name = 'buddybot_welcome_modal_dismissed';
                    var data        = {'action': 'bb_dismissible_notice','notice_name': notice_name,'nonce':bb_ajax_object.nonce};
                    jQuery.post(
                        bb_ajax_object.ajax_url,
                        data,
                        function(response) {
 
                        }
                    );
 
			}
		)
 
	}
);