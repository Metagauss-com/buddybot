<?php

namespace MetagaussOpenAI\Admin\Requests;

final class Settings extends \MetagaussOpenAI\Admin\Requests\MoRoot
{
    public function requestJs()
    {
        $this->sectionToggleJs();
    }

    private function sectionToggleJs()
    {
        echo '
        sectionToggle();
        $("#mgao-settings-section-select").change(sectionToggle);

        function sectionToggle() {
            $("#mgoa-settings-section-options > tbody").html("");
            $("#mgoa-settings-section-options-loader").removeClass("visually-hidden");
            let section = $("#mgao-settings-section-select").val();

            const data = {
                "action": "getOptions",
                "section": section,
                "nonce": "' . wp_create_nonce('get_options') . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                $("#mgoa-settings-section-options-loader").addClass("visually-hidden");
                $("#mgoa-settings-section-options > tbody").html(response);
            });
        }
        ';
    }
}