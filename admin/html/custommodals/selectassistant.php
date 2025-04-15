<?php

namespace BuddyBot\Admin\Html\CustomModals;

class SelectAssistant extends \BuddyBot\Admin\Html\CustomModals\MoRoot
{
    protected $modal_id = 'buddybot-select-assistant-modal';
    protected $close_outside = 'data-close-outside';

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
        echo '<input type="hidden" id="buddybot-selectassistant-last-id">';
    }

    protected function assistantList()
    {
        echo '<div class="buddybot-px-3 buddybot-mb-2">';
        echo '<div id="buddybot-select-assistant-modal-list" class="buddybot-list-group">';
        echo '</div>';
        $this->loadingSpinner();
        echo '</div>';
    }

    protected function loadingSpinner()
    {
        echo '<div class=" buddybot-d-flex buddybot-justify-content-center">';
        echo '<span id="buddybot-selectassistant-spinner" class="spinner is-active" aria-hidden="true"></span>';
        echo '</div>';
    }

    protected function loadMoreBtn()
    {
        echo '<div id="buddybot-selectassistant-load-more" class="buddybot-d-flex buddybot-justify-content-center">';
        $this->wordpressLoaderBtn('buddybot-selectassistant-load-more-btn', __('Load More', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), 'buddybot-btn-outline-black bb-btn-sm');
        echo '</div>';
    }

    protected function showCloseButton()
    {
        return true;
    }
}