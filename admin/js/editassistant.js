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

   // });

})(jQuery);