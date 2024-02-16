<?php

namespace MetagaussOpenAI\Admin\Requests;

class MoRoot extends \MetagaussOpenAI\Admin\MoRoot
{
    public function requestsJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->requestJs();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    protected function requestJs()
    {

    }
}