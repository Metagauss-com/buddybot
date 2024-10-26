<?php

namespace BuddyBot\Admin\Html\Views;

final class DataSync extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Data Sync', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->pageHeading($heading);
        $this->itemsList();
        $this->msgArea();
    }

    private function itemsList()
    {
        echo '<div class="list-group">';
        $this->postsItem();
        $this->commentsItem();
        echo '</div>';
    }

    private function listItem($type, $heading, $text)
    {
        $file_id = get_option('buddybot-' . $type . '-remote-file-id', '0');

        echo '<div class="list-group-item list-group-item-action w-50" ';
        echo 'data-buddybot-type="' . esc_attr($type) . '" ';
        echo 'data-buddybot-remote_file_id="' . esc_attr($file_id) . '" ';
        echo '>';
        
        echo '<div class="d-flex w-100 justify-content-between align-items-center">';
        
        echo '<div>';
        echo '<h5 class="mb-1">' . esc_html($heading) . '</h5>';
        echo '<p class="mb-1 fw-bold">' . esc_html($text) . '</p>';
        echo '</div>';
        
        echo '<div class="btn-group btn-group-sm" role="group">';
        $this->syncBtn($type);
        echo '</div>';
        
        echo '</div>';

        echo '<div class="buddybot-remote-file-status small" role="group">';
        echo esc_html(__('Checking status...', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</div>';

        echo '</div>';   
    }

    private function msgArea()
    {
        echo '<div class="buddybot-msgs small text-muted w-50 mt-4 visually-hidden" role="group">';
        echo '</div>';
    }

    private function syncBtn($type)
    {
        $btn_id = 'buddybot-sync-' . $type . '-btn';
        echo '<button id="' . esc_attr($btn_id) . '" type="button" ';
        echo 'class="buddybot-sync-btn btn btn-outline-dark" ';
        echo 'data-buddybot-type="' . esc_attr($type) .  '"';
        echo '>';
        $this->moIcon('directory_sync');
        echo '</button>';
    }

    private function postsItem()
    {
        $type = 'posts';
        $heading = __('WP Posts', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $text = __('Syncronize WordPress Posts with OpenAI.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->listItem($type, $heading, $text);
    }

    private function commentsItem()
    {
        $type = 'comments';
        $heading = __('Site Comments', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $text = __('Syncronize WordPress Comments with OpenAI.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->listItem($type, $heading, $text);
    }

    public function getInlineJs()
    {
        
    }

}