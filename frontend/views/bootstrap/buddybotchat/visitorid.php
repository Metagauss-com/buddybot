<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;
 
trait VisitorId
{
    protected function visitorIdHtml()
    {
        $html  = '<div class="modal fade buddybot-frontend-modal" id="buddybot-visitor-Id-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">';
        $html .= '<div class="modal-dialog modal-dialog-centered">';
        $html .= '<div class="modal-content">';
        $html .= $this->visitorIdHeader();
        $html .= $this->visitorIdBody();
        $html .= $this->visitorIdFooter();
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    protected function visitorIdHeader()
    {
        $html  = '<div class="modal-header border-0">';
        $html .= '<div class="modal-title fw-bold">';
        $html .= __('Information', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';
        //$html .= '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        $html .= '</div>';
        return $html;
    }

    protected function visitorIdBody()
    {
        $html  = '<div class="modal-body">';
        $html .= '<div class="mb-3">';
        $html .= '<label for="buddybot-visitor-Id" class="form-label">' . __('Enter your email address:', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label>';
        $html .= '<input type="email" id="buddybot-visitor-Id" name="buddybot-visitor-Id" class="form-control" />';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    protected function visitorIdFooter()
    {
        $html  = '<div class="modal-footer border-0">';
        // Cancel Button
        $html .= '<button id="buddybot-cancel-visitor-id-btn" type="button" class="btn btn-sm btn-secondary me-2" data-bs-dismiss="modal">';
        $html .= __('Cancel', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</button>';

        // Save Button
        $html .= '<button id="buddybot-save-visitor-id-btn" type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">';
        $html .= __('Save', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</button>';

        $html .= '</div>';
        return $html;
    }
}