<?php

namespace BuddyBot\Admin\Html\CustomModals;

class MoRoot extends \BuddyBot\Admin\Html\MoRoot
{
    protected $modal_id = '';

    public function getHtml()
    {
        echo '<div class="buddybot-modal" tabindex="-1" id="' . esc_attr($this->modal_id) . '">';
        echo '<div class="buddybot-modal-dialog ' . esc_attr($this->modalSize()) . '">';
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
        if ($this->showCloseButton()) {
            $this->closeButtonHtml();
        }
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
    protected function closeButtonHtml()
    {
        echo '<button type="button" class="buddybot-close-btn" data-modal="' . esc_attr($this->modal_id) . '" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" height="26px" viewBox="0 -960 960 960" width="26px" fill="#1f1f1f"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
        </button>';
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
    protected function showCloseButton()
    {

    }

    protected function modalSize()
    {

    }
}