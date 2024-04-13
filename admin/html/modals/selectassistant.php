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
        $this->listData();
        $this->assistantList();
        $this->loadMoreBtn();
    }

    protected function listData()
    {
        echo '<input type="hidden" id="mgao-selectassistant-last-id">';
    }

    protected function assistantList()
    {
        echo '<div class="px-3 mb-2" style="height: 600px; overflow: auto;">';
        echo '<div id="mgao-select-assistant-modal-list" class="list-group small">';
        echo '</div>';
        $this->loadingSpinner();
        echo '</div>';
    }

    protected function loadingSpinner()
    {
        echo '<div id="mgoa-selectassistant-spinner" class="d-flex align-items-center justify-content-center visually-hidden" style="min-height: 250px">';
        echo '<div class="spinner-border spinner-border-sm" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
        echo '</div>';
    }

    protected function loadMoreBtn()
    {
        echo '<div id="mgoa-selectassistant-load-more" class="text-center">';
        $this->loaderBtn('dark btn-sm', 'mgoa-selectassistant-load-more-btn', __('Load More', 'metagauss-openai'));
        echo '</div>';
    }
}