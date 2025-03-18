<?php

namespace BuddyBot\Admin\Html\Views;

final class VectorStore extends \BuddyBot\Admin\Html\Views\MoRoot
{

    public function getHtml()
    {

        $heading = __('AI Training', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->customHeading($heading);
        $this->alertContainer();
        $this->getVectorStore();
        $this->ProgressBar();
        $this->itemsList();
        $this->msgArea();
    }

    private function customHeading($heading)
    {
        echo '<div class="mb-1" style="display: flex; align-items: center;">';
        echo '<h3>';
        echo esc_html($heading);
        echo '</h3>';
        $this->createVectorStoreBtn();
        echo '</div>';
    }

    private function createVectorStoreBtn()
    {
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = !empty($vectorstore_data['id']) ? esc_attr($vectorstore_data['id']) : '';

        $btn_label = __('Create AI Training Knowledgebase', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        $id = 'buddybot-vectorstore-create';
        echo '<div class="ms-2">';

        echo '<input type="submit" id="' . esc_attr($id) . '" ';
        echo 'class="button button-outline visually-hidden" " value="' . esc_html($btn_label) . '">';
        echo '<span class="spinner is-active" style="display:none;" aria-hidden="true"></span>';

        //$this->wordpressLoaderBtn($id, $btn_label, 'visually-hidden');

        $this->createHiddenField('buddybot_vector_store_id', $vectorstore_id);

        echo '</div>';
    }

    private function getVectorStore()
    {
        echo '<div id="buddybot-get-vectorstore" class="small">';
        echo '<div id="buddybot-vectorstore-section">';
        echo '<p id="buddybot-vectorstoreName" style="display: none;">Loading...</p>'; 
        echo '</div>';
        echo '</div>';
    }

    private function itemsList()
    {
        echo '<div class="list-group">';
        $this->postsItem();
        $this->commentsItem();
        echo '</div>';
    }

    private function ProgressBar()
    {
        echo '<div class="mt-1 w-50 m-0" id="buddybot-progress-container">';
        echo '<div class="d-flex align-items-center gap-1" id="buddybot-progress-wrapper">';
        echo '<div id="buddybot-ProgressBar" class="progress flex-grow-1" role="progressbar" aria-label="Success example" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: 5px; opacity:0;">';
        echo '<div class="progress-bar bg-primary" style="width: 0%"></div>';
        echo '</div>';
        echo '<div id="buddybot-progressbar-percentage" style="opacity: 0;">0%</div>';
        echo '</div>';
        echo '<p id="buddybot-sync-msgs" class="d-flex align-items-center gap-1 mt-0" style="opacity: 0;">';
        echo '<span id="buddybot-ProgressBar-icon" class="material-symbols-outlined fs-6"></span>';
        echo '<span id="buddybot-message" class="flex-grow-1 small">' . esc_html__('Sync processing...', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
        echo '</p></div>';
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
        echo '<h5 class="mb-1 fs-5 m-0">' . esc_html($heading) . '</h5>';
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
        $heading = __('Posts', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $text = __('Train AI Assistant with your site Posts.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->listItem($type, $heading, $text);
    }

    private function commentsItem()
    {
        $type = 'comments';
        $heading = __('Comments', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $text = __('Train AI Assistant with your site Comments.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->listItem($type, $heading, $text);
    }
}
