<?php

namespace MetagaussOpenAI\Admin\Html\Elements\Playground;

class ChatBubble extends \MetagaussOpenAI\Admin\Html\Elements\Playground\MoRoot
{
    private $message;
    protected $roles;

    protected function setRoles()
    {
        $this->roles = array('user', 'assistant');
    }

    public function setMessage($message = '')
    {
        if (!is_object($message)) {
            return;
        }

        $this->message = $message;
    }

    public function getHtml()
    {
        return $this->messageHtml();
    }

    protected function messageHtml()
    {
        $role = $this->message->role;
        $method = $role . 'MessageHtml';
        
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    protected function userMessageHtml()
    {
        $args = array('default' => 'retro');
        $img_url = get_avatar_url(get_current_user_id(), $args);
        
        $html = '<div class="mgoa-playground-messages-list-item d-flex justify-content-end my-2" id="' . esc_attr($this->message->id) . '">';

        $html .= $this->messageImage($img_url);

        $html .= '<div>';

        $html .= '<div class="p-3 bg-primary text-white rounded-4 rounded-top-0 rounded-end-4" style="max-width: 500px;">';
        
        foreach ($this->message->content as $content) {
            $html .= $content->text->value;
        }

        $html .= '</div>';

        $html .= $this->messageDate();

        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    protected function assistantMessageHtml()
    {
        $img_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-logomark.svg';
        
        $html = '<div class="mgoa-playground-messages-list-item d-flex justify-content-start my-2" id="' . esc_attr($this->message->id) . '">';

        $html .= $this->messageImage($img_url);

        $html .= '<div>';

        $html .= '<div class="p-3 bg-light rounded-4 rounded-4 rounded-top-0 rounded-end-4" style="max-width: 500px;">';
        
        foreach ($this->message->content as $content) {
            $html .= $this->parseFormatting($content->text->value);
        }
        
        $html .= '</div>';

        $html .= $this->messageDate();

        $html .= '</div>';

        $html .= '</div>';
        return $html;
    }

    private function messageImage($img_url)
    {
        $html = '<div class="me-2">';
        $html .= '<img width="28" class="rounded-circle border" src="' . esc_url($img_url) . '">';
        $html .= '</div>';
        return $html;
    }

    private function messageDate()
    {
        $date_format = $this->config->getProp('date_format');
        $time_format = $this->config->getProp('time_format');

        $message_date = wp_date($date_format, $this->message->created_at);
        $message_time = wp_date($time_format, $this->message->created_at);

        $message_day = wp_date('j', $this->message->created_at);
        $current_day = wp_date('j');

        if ($message_day === $current_day) {
            $message_date = __('Today', 'metagauss-openai');
        }

        if ((absint($current_day) - absint($message_day)) === 1) {
            $message_date = __('Yesterday', 'metagauss-openai');
        }


        $html = '<div class="small text-end text-muted mt-2 me-2">';
        $html .= esc_html($message_date . ', ' . $message_time);
        $html .= '</div>';
        return $html;
    }

    protected function parseFormatting($text)
    {
        $bold = '/(?<=\*\*)(.*?)(?=\*\*)/';
        $text = preg_replace($bold, '<strong>$1</strong>', $text);
        $text = str_replace('**', '', $text);
        return  nl2br($text);
    }
}