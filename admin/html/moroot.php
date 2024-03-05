<?php

namespace MetagaussOpenAI\Admin\Html;

class MoRoot extends \MetagaussOpenAI\Admin\MoRoot
{
    protected function pageHeading($heading)
    {
        echo '<div class="mb-3">';
        echo '<h3>';
        echo esc_html($heading);
        echo '</h3>';
        echo '</div>';
    }
    public function getHtml()
    {
        
    }
}