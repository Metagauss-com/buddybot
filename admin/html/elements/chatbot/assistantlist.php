<?php

namespace BuddyBot\Admin\Html\Elements\Chatbot;

class AssistantList extends \BuddyBot\Admin\Html\Elements\Chatbot\MoRoot
{
    protected $item;

    public function listItem($item)
    {
        $this->item = $item;
    }

    public function getHtml()
    {
        $html = '<a href="#" class="buddybot-list-group-item" ';
        $html .= 'data-mgao-id="' . esc_attr($this->item->id) . '" data-mgao-name="' . esc_attr($this->item->name) . '">';
        $html .= $this->assistantName();
        $html .= $this->assistantId();
        $html .= $this->createdOn();
        $html .= '</a>';
        return $html;
    }

    private function assistantName()
    {
        $name = $this->item->name;

        if (empty($name)) {
            $name = __('Unnamed', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        $html = '<div class="buddybot-small buddybot-fw-bold buddybot-text-break">';
        $html .= $name;
        $html .= '</div>';
        return $html;
    }

    private function assistantId()
    {
        $html = '<div class="buddybot-small">';
        $html .= $this->item->id;
        $html .= '</div>';
        return $html;
    }

    private function createdOn()
    {
        $date_format = $this->config->getProp('date_format');
        $time_format = $this->config->getProp('time_format');
        $timezone = wp_timezone();
 
        $message_date = wp_date($date_format, $this->item->created_at, $timezone);
        $message_time = wp_date($time_format, $this->item->created_at, $timezone);
        
        $html = '<div class="buddybot-small">';
        $html .= __('Created On', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= ' ';
        $html .= esc_html($message_date . ', ' . $message_time);
        $html .= '</div>';
        return $html;
    }
}