<?php

namespace BuddyBot\Admin\Html\Views;

final class VectorStore extends \BuddyBot\Admin\Html\Views\MoRoot
{

    public function getHtml()
    {

        $heading = esc_html__('AI Training', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->pageHeading($heading);
        $this->alertContainer();
        $this->getVectorStore();
        $this->createVectorStoreBtn();
        //$this->deleteVectorStoreBtn();
        $this->itemsList();
        $this->msgArea();
    }

    private function createVectorStoreBtn()
    {

        $btn_label = esc_html__('Create AI Training Knowledgebase', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        $id = 'buddybot-vectorstore-create';
        echo '<div>';

        $this->loaderBtn('dark btn-sm mb-1 visually-hidden', $id, $btn_label);
        $this->createHiddenField('buddybot_vector_store_id');

        echo '</div>';
    }

    private function getVectorStore()
    {
        echo '<div id="buddybot-get-vectorstore" class="small mb-4">';

        echo '<div id="buddybot-vectorstore-section">';

        echo '<p id="buddybot-vectorstoreName" style="display: none;">Loading...</p>'; 
        echo '<div id="buddybot-assistants-loading-spinner" class="d-flex justify-content-center" style="display: block;">';
        echo '<div class="spinner-border text-dark" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        
        echo '</div>';
        
        echo ' <div id="buddybot-get-files"> </div>';
    }

    private function deleteVectorStoreBtn()
    {
        $btn_label = esc_html__('Delete AI Training Knowledgebase', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        $id = 'buddybot-vectorstore-delete';
        echo '<div>';

        $this->loaderBtn('dark btn-sm', $id, $btn_label);

        echo '</div>';
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

        echo '<div class="buddybot-remote-file-status buddybot-remote-file-status'. esc_attr($type) .' text-break" role="group">';
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
        $heading = esc_html__('Posts', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $text = esc_html__('Train AI Assistant with your site Posts.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->listItem($type, $heading, $text);
    }

    private function commentsItem()
    {
        $type = 'comments';
        $heading = esc_html__('Comments', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $text = esc_html__('Train AI Assistant with your site Comments.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->listItem($type, $heading, $text);
    }
}
