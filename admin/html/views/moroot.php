<?php

namespace MetagaussOpenAI\Admin\Html\Views;

class MoRoot extends \MetagaussOpenAI\Admin\Html\MoRoot
{
    protected function alertContainer()
    {
        echo '<div id="mo-alert-container" class="alert alert-danger small w-50" role="alert" style="display:none;">';
        echo '</div>';
    }

    protected function moSpinner()
    {
        echo '<div class="mo-dataload-spinner spinner-border spinner-border-sm text-primary" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
    }
}