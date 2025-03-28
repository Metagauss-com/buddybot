jQuery(document).ready(function($) {
        // Handle click event on any element with the data-popover attribute
        $('[data-popover="true"]').click(function() {
            var targetPopover = $($(this).data('target'));  // Get the popover element using the data-target attribute
    
            // Toggle the popover visibility
            targetPopover.toggle();  
        });
    
        // Optionally, close the popover if clicked outside of the trigger or the popover
        $(document).click(function(event) {
            // Check if the click is outside the trigger element and the popover
            if (!$(event.target).closest('[data-popover="true"], .buddybot-popover').length) {
                //$('.buddybot-popover').hide();  // Hide all popovers if clicked outside
            }
        });
});
    