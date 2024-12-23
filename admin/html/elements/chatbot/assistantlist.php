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
        $html = '<a href="#" class="list-group-item list-group-item-action" data-bs-dismiss="modal" ';
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

        $html = '<div class="small fw-bold text-break">';
        $html .= $name;
        $html .= '</div>';
        return $html;
    }

    private function assistantId()
    {
        $html = '<div class="small">';
        $html .= $this->item->id;
        $html .= '</div>';
        return $html;
    }

    private function createdOn()
    {
        $format = get_option('date_format') . ' ' . get_option('time_format');
        $html = '<div class="small">';
        $html .= __('Created On', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= ' ';
        $html .= wp_date($format, $this->item->created_at);
        $html .= '</div>';
        return $html;
    }
}