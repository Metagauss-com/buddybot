<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

use BuddyBot\Traits\Singleton;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SecurityChecks;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SingleConversation;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\DeleteConversation;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\VisitorId;

class BuddybotChat extends \BuddyBot\Frontend\Views\Bootstrap\MoRoot
{
    use Singleton;
    use SecurityChecks;
    use SingleConversation;
    use DeleteConversation;
    use VisitorId;

    protected $conversations;
    protected $chatbot;
    protected $timezone;

    public function shortcodeHtml($atts, $content = null)
    {
        $default_atts = array(
            'id' => $this->buddybotId()
        );
        $this->atts = shortcode_atts( $default_atts, $atts );

        $html = $this->securityChecksHtml();
        $this->shortcodeJs();

        if (!$this->errors) {
            $html .= $this->deleteConversationModalHtml();
            $html .= $this->visitorIdHtml();
            $html .= $this->alertsHtml();
            $html .= $this->assistantId();
            $html .= $this->conversationListWrapper();
            $html .= $this->singleConversationHtml($this->atts);
        }

        return $html;
    }

    protected function shortcodeJs()
    {
        wp_enqueue_script('buddybot-chatbot-script', $this->config->getRootUrl() . 'frontend/js/buddybotchat.js', array('jquery'), BUDDYBOT_PLUGIN_VERSION, true);
        $js = \BuddyBot\Frontend\Requests\BuddybotChat::getInstance();
        wp_add_inline_script('buddybot-chatbot-script', $js->localJs());
    }

    protected function buddybotId()
    {
        $id = $this->sql->getDefaultBuddybotId();
        return $id;
    }

    protected function alertsHtml()
    {
        $html = '<div class="buddybot-chat-conversation-alert alert alert-danger small" data-bb-alert="danger" role="alert" style="display:none;">';
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
        
        $html .= '<div class="small fw-bold me-2">';
        $html .= __('Select Conversation or', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';
        
        $html .= '<button id="buddybot-chat-conversation-start-new" type="button" class="btn btn-dark btn-sm px-3 rounded-2">';
        $html .= __('Start New', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</button>';
        
        $html .= '</div>';

        $html .= '<div id="buddybot-chat-conversation-list-loader" class="text-muted">';
        $html .= __('Loading conversations...', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</div>';

        $html .= '<div id="buddybot-chat-conversation-list-wrapper">';
        $html .= '</div>';
        return $html;
    }

    public function conversationList($timezone)
    {
        $this->timezone = $timezone;

        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $this->conversations = $this->sql->getConversationsByUserId($user_id);
        } else { 
            $cookie_data = json_decode(stripslashes($_COOKIE['buddybot_session_data']), true);

            if (isset($cookie_data['session_id']) && !empty($cookie_data['session_id'])) {
                $this->conversations = $this->sql->getConversationsBySessionId($cookie_data['session_id']);
            } else {
                $this->conversations = '';
            }
        }
        
        if (!empty($this->conversations)) {
            $this->listHtml();
        } else {
            $this->noCoversationHistoryHtml();
        }
    }

    protected function listHtml()
    {
        echo '<ol class="list-group list-group-numbered small px-0">';

        foreach ($this->conversations as $conversation) {
            echo '<li class="list-group-item list-group-item-action m-0 d-flex justify-content-between align-items-start bg-transparent"';
            echo 'data-bb-threadid="' . esc_html($conversation->thread_id) . '" role="button">';
            echo '<div class="ms-2 me-auto text-start">';
            echo '<div class="fw-bold text-break ">' . esc_html($conversation->thread_name) . '</div>';
            echo '<div class="text-muted small text-start">' . esc_html($this->conversationDate($conversation->created)) . '</div>';
            echo '</div>';
            echo '</li>';
        }
        
        echo '</ol>';
    }

    protected function conversationDate($date_string)
    {
        $timezone = new \DateTimeZone($this->timezone);
        $timestamp = strtotime($date_string);
        return wp_date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp, $timezone);
    }

    protected function noCoversationHistoryHtml()
    {
        $img_url = $this->config->getRootUrl() . 'frontend/images/buddybotchat/bootstrap/zero-conversations.png';
        echo '<div class="mt-4 text-center">';
        
        echo '<div class="my-4">';
        echo '<img width="250" src="' . esc_url($img_url) . '">';
        echo '</div>';
        
        echo '<div>';
        esc_html_e('Sorry, you have no past conversations. Please start a new one.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';
        
        echo '</div>';
    }
}