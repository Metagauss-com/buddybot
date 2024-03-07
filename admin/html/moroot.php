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

    protected function moIcon($icon)
    {
        echo '<span class="material-symbols-outlined" style="font-size:20px;vertical-align:sub;">';
        echo esc_html($icon);
        echo '</span>';
    }

    public function getHtml()
    {
        
    }
}