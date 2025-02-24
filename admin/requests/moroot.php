<?php

namespace BuddyBot\Admin\Requests;

class MoRoot extends \BuddyBot\Admin\MoRoot
{
    public function requestsJs()
    {

        ob_start();
        
        echo 'jQuery(document).ready(function($){';

        $this->showAlertJs();
        $this->hideAlertJs();
        $this->loaderBtnJs();
        $this->disableFieldsJs();
        $this->showHideJs();
        $this->requestJs();
        $this->showToastjs();
        
        echo '});';

        return ob_get_clean();
    }

    protected function showHideJs()
    {
        echo'
        function showHide(obj, primary, secondary, trinary) {
            var isChecked = jQuery(obj).is(":checked");
        
            if (isChecked) {
                jQuery("#" + primary).show(500);
                if (secondary !== "") jQuery("#" + secondary).hide(500);
                if (trinary !== "") jQuery("#" + trinary).hide(500);
            } else {
                jQuery("#" + primary).hide(500);
                if (secondary !== "") jQuery("#" + secondary).show(500);
                if (trinary !== "") jQuery("#" + trinary).show(500);
            }
        }
        ';
    }

    protected function showAlertJs()
    {
        echo '
        function showAlert(message = "") {
            $("#buddybot-alert-container p").html(message);
            $("#buddybot-alert-container").addClass("notice").show();
        }
        ';
    }

    protected function hideAlertJs()
    {
        echo '
        function hideAlert(message = "") {
            $("#buddybot-alert-container p").html("");
            $("#buddybot-alert-container").removeClass("notice").hide();
        }
        ';
    }

    protected function loaderBtnJs()
    {
        echo '
        function showBtnLoader(btnId) {
            $(btnId).prop("disabled", true);
            $(btnId).children(".buddybot-loaderbtn-label").addClass("visually-hidden");
            $(btnId).children(".buddybot-loaderbtn-spinner").removeClass("visually-hidden");
        }

        function hideBtnLoader(btnId) {
            $(btnId).prop("disabled", false);
            $(btnId).children(".buddybot-loaderbtn-label").removeClass("visually-hidden");
            $(btnId).children(".buddybot-loaderbtn-spinner").addClass("visually-hidden");
        }

        function showWordpressLoader(btnId) {
            $(btnId).prop("disabled", true);
            $(btnId).next(".spinner").css("display", "inline-block");
        }

        function hideWordpressLoader(btnId) {
            $(btnId).prop("disabled", false);
            $(btnId).next(".spinner").css("display", "none");
        }
        ';
    }

    protected function disableFieldsJs()
    {
        echo '
        function disableFields(isDisabled) {
            $(".buddybot-item-field").each(function(){
                $(this).prop("disabled", isDisabled);
            });
        }
        ';
    }

    protected function showToastjs()
    {
        echo'
        function showToast(type, message) {
            var toastContainer = $("#buddybot-toast-container .buddybot-toast");
            var toastMessage = $(".toast-message");

            toastMessage.text(message);
            toastContainer.removeClass("buddybot-toast-success buddybot-toast-error");

            if (type === "success") {
                toastContainer.addClass("buddybot-toast-success");
            } else if (type === "error") {
                toastContainer.addClass("buddybot-toast-error");
            }

            toastContainer.addClass("show");

            setTimeout(function () {
                toastContainer.removeClass("show");
            }, 3000);
        }
        ';

    }

    protected function requestJs()
    {

    }
}