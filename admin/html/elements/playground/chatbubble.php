<?php

namespace BuddyBot\Admin\Html\Elements\Playground;

class ChatBubble extends \BuddyBot\Admin\Html\Elements\Playground\MoRoot
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
        
        $html = '<div class="buddybot-playground-messages-list-item buddybot-d-flex   buddybot-my-4 buddybot-justify-content-end buddybot-align-self-end" id="' . esc_attr($this->message->id) . '">';

        $html .= $this->messageImage($img_url);

        $html .= '<div>';

        $html .= '<div class=" buddybot-text-white buddybot-chat-border-radius buddybot-ms-2 buddybot-bg-primary-wordpress buddybot-text-break buddybot-px-3 buddybot-py-2 buddybot-align-self-end buddybot-text-small buddybot-user-message">';
        
        foreach ($this->message->content as $content) {
            
            if ($content->type === 'text') {
                $html .= $this->parseFormatting($content->text->value);
            }

            if ($content->type === 'image_file') {
                $html .= $this->parseImage($content->image_file->file_id);
            }

        }

        if (!empty($this->message->file_ids)) {
            foreach ($this->message->file_ids as $file_id) {
                $html .= $this->parseFile($file_id);
            }
        }

        $html .= '</div>';

        $html .= $this->messageDate();

        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }

    protected function assistantMessageHtml()
    {
        $img_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-logomark.png';

        
        $html = '<div class="buddybot-playground-messages-list-item buddybot-d-flex   buddybot-my-4 sss" id="' . esc_attr($this->message->id) . '">';

        $html .= $this->messageImage($img_url);

        $html .= '<div>';

        $html .= '<div class="buddybot-p-2 buddybot-bg-light-gray buddybot-chat-border-radius buddybot-assistant-message">';
        
        foreach ($this->message->content as $content) {
            
            if ($content->type === 'text') {
                $html .= $this->parseFormatting($content->text->value);
            }

            if ($content->type === 'image_file') {
                $html .= $this->parseImage($content->image_file->file_id);
            }

        }
        
        $html .= '</div>';

        $html .= $this->messageDate();

        $html .= '</div>';

        $html .= '</div>';
        return $html;
    }

    private function messageImage($img_url)
    {
        $html = '<div class="buddybot-me-2">';
        $html .= '<img width="28" class=" buddybot-border-radius-50" src="' . esc_url($img_url) . '">';
        $html .= '</div>';
        return $html;
    }

    private function messageDate()
    {
        $date_format = $this->config->getProp('date_format');
        $time_format = $this->config->getProp('time_format');
        $timezone = wp_timezone();

        $message_date = wp_date($date_format, $this->message->created_at, $timezone);
        $message_time = wp_date($time_format, $this->message->created_at, $timezone);

        $message_day = wp_date('j', $this->message->created_at, $timezone);
        $current_day = wp_date('j', $timezone);

        if ($message_day === $current_day) {
            $message_date = __('Today', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        if ((absint($current_day) - absint($message_day)) === 1) {
            $message_date = __('Yesterday', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        $html = '<div class="buddybot-text-muted buddybot-text-align-right buddybot-mt-2 buddybot-me-2 buddybot-message-date">';
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

    protected function parseFile($file_id)
    {
        $url = 'https://api.openai.com/v1/files/' . $file_id;

        $headers = array(
            'Authorization' => 'Bearer ' . $this->options->getOption('openai_api_key'),
        );
    
        $response = wp_remote_get($url, array(
            'headers' => $headers,
        ));
    
        if (is_wp_error($response)) {
            return '';
        }
    
        $output = json_decode(wp_remote_retrieve_body($response));

        $type = pathinfo($output->filename, PATHINFO_EXTENSION);
        
        $html = '<div class="buddybot-mt-2 buddybot-bg-dark buddybot-p-3  buddybot-d-flex buddybot-align-items-center">';
        
        $html .= '<div class="buddybot-me-2">';
        $html .= '<img src="' . $this->config->getRootUrl() . 'admin/html/images/fileicons/file.png" height="24">';
        $html .= '</div>';
        
        $html .= '<div class="buddybot-text-small">';

        $html .= '<div class="buddybot-text-small buddybot-fw-bold">';
        $html .= $output->filename;
        $html .= '</div>';

        $html .= '<div class="buddybot-text-small">';
        $html .= $this->fileSize($output->bytes);
        $html .= '</div>';
        
        $html .= '</div>';
        
        $html .= '</div>';

        return $html;
    }

    protected function parseImage($image_id)
    {
        $url = 'https://api.openai.com/v1/files/' . $image_id . '/content';
    
        $headers = array(
            'Authorization' => 'Bearer ' . $this->options->getOption('openai_api_key'),
        );
    
        $response = wp_remote_get($url, array(
            'headers' => $headers,
        ));
    
        if (is_wp_error($response)) {
            return;
        }
    
        $output = wp_remote_retrieve_body($response);
    
        $html = '<div class="buddybot-mb-2 buddybot-bg-secondary  buddybot-p-3 ">';
        $html .= '<img src="data:image/png;base64,' . base64_encode($output) . '" width="96">';
        $html .= '</div>';
    
        return $html;
    }
}