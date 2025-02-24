(function ($) {
    'use strict';

    //$(document).ready(function () {

        //Range Value of Temperature
        $('#buddybot-assistanttemperature-range').on('input', function () {
            var value = $(this).val();
            $('#buddybot-assistanttemperature-value').text(value);
            
        });

        var initialValue = $('#buddybot-assistanttemperature-range').val();
        $('#buddybot-assistanttemperature-value').text(initialValue);
        

        //Range Value of Top_p
        $('#buddybot-assistanttopp-range').on('input', function() {
            var value = $(this).val();
            $('#buddybot-assistanttopp-value').text(value);    
        });

        var initialValue = $('#buddybot-assistanttopp-range').val();
        $('#buddybot-assistanttopp-value').text(initialValue);

   // });

})(jQuery);