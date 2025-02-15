<?php

namespace BuddyBot\Admin\Html\Modals;

class SelectAssistant extends \BuddyBot\Admin\Html\Modals\MoRoot
{
    protected $modal_id = 'buddybot-select-assistant-modal';

    protected function modalTitle()
    {
        return __('Select Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
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
        echo '<div class="px-3 mb-2">';
        echo '<div id="mgao-select-assistant-modal-list" class="list-group small">';
        echo '</div>';
        $this->loadingSpinner();
        echo '</div>';
    }

    protected function loadingSpinner()
    {
        echo '<div id="buddybot-selectassistant-spinner" class="d-flex align-items-center justify-content-center visually-hidden">';
        echo '<div class="spinner-border spinner-border-sm" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
        echo '</div>';
    }

    protected function loadMoreBtn()
    {
        echo '<div id="buddybot-selectassistant-load-more" class="text-center">';
        $this->loaderBtn('outline-dark btn-sm', 'buddybot-selectassistant-load-more-btn', __('Load More', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</div>';
    }
}