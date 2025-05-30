<?php

namespace BuddyBot\Admin\Html\Views;

final class Assistants extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Assistants', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->pageHeading($heading);
        $this->pageBtns();
        $this->alertContainer();
        $this->assistantsTable();
        $this->noMoreAssistants();
        $this->loadMoreBtn();
    }

    public function pageBtns()
    {
        $add_assistant_page = get_admin_url() . 'admin.php?page=buddybot-editassistant';
        echo '<div class="mb-3">';
        echo '<a class="btn btn-dark btn-sm" role="button"';
        echo 'href="' . esc_url($add_assistant_page) . '"';
        echo '>';
        echo esc_html(__('Create New Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</a>';
        echo '</div>';
    }

    private function assistantsTable()
    {
        echo '<table class="buddybot-org-assistants-table table table-sm">';
        $this->tableHeader();
        $this->tableBody();
        echo '</table>';
    }

    private function tableHeader()
    {
        echo '<thead>';
        echo '<tr>';
        echo '<th class="col buddybot-col-no" scope="col">' . esc_html(__('No.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-col-name" scope="col">' . esc_html(__('Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-col-description" scope="col">' . esc_html(__('Description', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-col-model" scope="col">' . esc_html(__('Model', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-col-id" scope="col">' . esc_html(__('ID', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</th>';
        echo '<th class="col buddybot-col-btn" scope="col"></th>';
        echo '</tr>';
        echo '</thead>';
    }

    private function tableBody()
    {
        echo '<tbody>';
        echo '<tr id="buddybot-assistants-loading-spinner">';
        echo '<td colspan="6" class="p-5">';
        echo '<div class="spinner-border text-dark d-flex justify-content-center mx-auto" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
        echo '</td>';
        echo '</tbody>';
    }

    private function noMoreAssistants()
    {
        echo '<div id="buddybot-assistants-no-more" class="text-center small fw-bold visually-hidden">';
        esc_html_e('There are no more Assistants to load.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->moIcon('sentiment_satisfied');
        echo '</div>';
    }

    private function loadMoreBtn()
    {
        echo '<div class="text-center">';
        $this->loaderBtn('outline-dark btn-sm visually-hidden', 'buddybot-assistants-load-more-btn', __('Load More', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</div>';
    }
    
}