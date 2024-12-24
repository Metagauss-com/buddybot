jQuery( document ).ready(
    function(){
		jQuery( ".bb-dismissible" ).click(
            function()
            {
 
                    var notice_name = jQuery( this ).attr( 'id' );
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