<?php

namespace MetagaussOpenAI\Admin\Requests;

class MoRoot extends \MetagaussOpenAI\Admin\MoRoot
{
    public function requestsJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->showAlert();
        $this->hideAlert();
        $this->requestJs();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    protected function showAlert()
    {
        echo '
        function showAlert(message = "") {
            $("#mo-alert-container").html(message);
            $("#mo-alert-container").show();
        }
        ';
    }

    protected function hideAlert()
    {
        echo '
        function hideAlert(message = "") {
            $("#mo-alert-container").html("");
            $("#mo-alert-container").hide();
        }
        ';
    }

    protected function requestJs()
    {

    }
}