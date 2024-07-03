<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

use BuddyBot\Traits\Singleton;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SecurityChecks;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SingleConversation;

class BuddybotChat extends \BuddyBot\Frontend\Views\Bootstrap\MoRoot
{
    use Singleton;
    use SecurityChecks;
    use SingleConversation;
    protected $conversations;
    protected $chatbot;

    public function shortcodeHtml($atts, $content = null)
    {
        $this->atts = shortcode_atts( array(
            'id' => $this->buddybotId()
        ), $atts );

        $html = $this->securityChecksHtml();

        if (!$this->errors) {
            $html .= $this->alertsHtml();
            $html .= $this->assistantId();
            $html .= $this->conversationListWrapper();
            $html .= $this->singleConversationHtml();
        }

        return $html;
    }

    protected function buddybotId()
    {
        $id = $this->sql->getDefaultBuddybotId();
        return $id;
    }

    protected function alertsHtml()
    {
        $html = '<div class="buddybot-chat-conversation-alert alert alert-danger small" data-bb-alert="danger" role="alert">';
        $html .= '</div>';
        return $html;
    }

    protected function assistantId()
    {
        $html  = '<input id="buddybot-chat-conversation-assistant-id" type="hidden" ';
        $html .= 'value="' . esc_attr($this->chatbot->assistant_id) . '">';
        return $html;
    }

    private function conversationListWrapper()
    {
        $html  = '<div id="buddybot-chat-conversation-list-header" class="d-flex justify-content-start align-items-center">';
        
        $html .= '<div class="fs-6 fw-bold text-uppercase me-2">';
        $html .= __('Select Conversation or', 'buddybot');
        $html .= '</div>';
        
        $html .= '<button id="buddybot-chat-conversation-start-new" type="button" class="btn btn-warning btn-sm px-3 rounded-4">';
        $html .= __('Start New', 'buddybot');
        $html .= '</button>';
        
        $html .= '</div>';

        $html .= '<div id="buddybot-chat-conversation-list-loader" class="text-muted">';
        $html .= __('Loading conversations...', 'buddybot');
        $html .= '</div>';

        $html .= '<div id="buddybot-chat-conversation-list-wrapper">';
        $html .= '</div>';
        return $html;
    }

    public function conversationList()
    {
        $user_id = get_current_user_id();
        $this->conversations = $this->sql->getConversationsByUserId($user_id);
        
        if (!empty($this->conversations)) {
            $this->listHtml();
        }
    }

    protected function listHtml()
    {
        echo '<ol class="list-group list-group-numbered small">';

        foreach ($this->conversations as $conversation) {
            echo '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-start border-dark bg-transparent"';
            echo 'data-bb-threadid="' . $conversation->thread_id . '" role="button">';
            echo '<div class="ms-2 me-auto">';
            echo '<div class="fw-bold">' . $conversation->thread_name . '</div>';
            echo '<div class="text-muted small text-end">' . $conversation->created . '</div>';
            echo '</div>';
            echo '</li>';
        }
        
        echo '</ol>';
    }
}