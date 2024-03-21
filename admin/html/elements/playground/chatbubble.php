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
        
        $html = '<div class="d-flex justify-content-end my-2" id="' . esc_attr($this->message->id) . '">';

        $html .= $this->messageImage($img_url);

        $html .= '<div>';

        $html .= '<div class="p-3 bg-primary text-white rounded-4" style="max-width: 500px;">';
        
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
        
        $html = '<div class="d-flex justify-content-start my-2" id="' . esc_attr($this->message->id) . '">';

        $html .= $this->messageImage($img_url);

        $html .= '<div>';

        $html .= '<div class="p-3 bg-light rounded-4" style="max-width: 500px;">';
        
        foreach ($this->message->content as $content) {
            $html .= $content->text->value;
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
        $format = $this->config->getProp('date_format');

        $html = '<div class="small text-end text-muted mt-2 me-2">';
        $html .= wp_date($format, $this->message->created_at);
        $html .= '</div>';
        return $html;
    }
}