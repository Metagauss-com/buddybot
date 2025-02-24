<?php

namespace BuddyBot\Admin\Html\CustomModals;

class MoRoot extends \BuddyBot\Admin\Html\MoRoot
{
    protected $modal_id = '';

    public function getHtml()
    {
        echo '<div class="buddybot-modal fade" tabindex="-1" id="' . esc_attr($this->modal_id) . '">';
        echo '<div class="buddybot-modal-dialog">';
        echo '<div class="buddybot-modal-content">';
        $this->modalHeader();
        $this->modalBody();
        $this->modalFooter();
        echo '</div></div></div>';
    }

    protected function modalHeader()
    {
        echo '<div class="buddybot-modal-header">';
        echo '<h5 class="buddybot-modal-title">';
        echo esc_html($this->modalTitle());
        echo '</h5>';
        //echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        echo '</div>';
    }

    protected function modalBody()
    {
        echo '<div class="buddybot-modal-body">';
        $this->bodyContent();
        echo '</div>';
    }

    protected function modalFooter()
    {
        echo '<div class="buddybot-modal-footer">';
        $this->footerContent();
        echo '</div>';
    }

    protected function bodyContent()
    {

    }

    protected function footerContent()
    {

    }

    protected function modalTitle()
    {
        
    }
}