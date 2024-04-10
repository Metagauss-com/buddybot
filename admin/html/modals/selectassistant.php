<?php

namespace MetagaussOpenAI\Admin\Html\Modals;

class SelectAssistant extends \MetagaussOpenAI\Admin\Html\Modals\MoRoot
{
    protected $modal_id = 'mgoa-select-assistant-modal';

    protected function modalTitle()
    {
        return __('Select Assistant', 'metagauss-openai');
    }

    protected function bodyContent()
    {
        echo '<div class="px-3" style="height: 500px; overflow: auto;">';
        echo '<div id="mgao-select-assistant-modal-list" class="list-group small">';
        echo '</div>';
        echo '</div>';
    }
}