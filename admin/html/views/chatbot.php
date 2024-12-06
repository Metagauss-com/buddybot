<?php

namespace BuddyBot\Admin\Html\Views;

final class ChatBot extends \BuddyBot\Admin\Html\Views\MoRoot
{
    protected $chatbot_id = 0;
    protected $is_edit = false;
    protected $chatbot;
    protected $heading;
    protected $first_id;

    protected function setChatbotId()
    {
        if (!empty($_GET['chatbot_id'])) {
            $this->chatbot_id = absint($_GET['chatbot_id']);
        }
    }

    protected function setIsEdit()
    {
        $chatbot = $this->sql->getItemById('chatbot', $this->chatbot_id);

        if (is_object($chatbot)) {
            $this->is_edit = true;
            $this->chatbot = $chatbot;
        }
    }

    protected function setHeading()
    {
        if ($this->is_edit) {
            $this->heading = esc_html(__('Edit BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        } else {
            $this->heading = esc_html(__('New BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        }
    }

    protected function useSingleChatbot()
    {
        $sql = \BuddyBot\Admin\Sql\Chatbot::getInstance();
        $this->first_id = $sql->getFirstChatbotId();

        if (!$this->first_id) {
            return;
        }
    }

    protected function pageModals()
    {
        $select_assistant = new \BuddyBot\Admin\Html\Modals\SelectAssistant();
        $select_assistant->getHtml();
    }

    public function getHtml()
    {
        $this->useSingleChatbot();
        $this->pageModals();
        $this->pageSuccessAlert();
        $this->pageErrors();
        $this->pageHeading($this->heading);
        $this->chatbotShortcode();
        $this->chatbotOptions();
        $this->saveBtn();
    }

    private function pageSuccessAlert()
    {
        $success = (isset($_GET['success']) and $_GET['success'] == 1) ? 1 : 0;

        if (!$success) {
            return;
        }

        echo '<div id="buddybot-chatbot-success" class="notice notice-success mb-3 ms-0">';
        echo '<p id="buddybot-chatbot-success-message" class="fw-bold">' . esc_html(__('BuddyBot updated successfully.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</p>';
        echo '</div>';
    }

    private function pageErrors()
    {
        echo '<div id="buddybot-chatbot-errors" class="notice notice-error settings-error mb-3 ms-0" style="display:none;">';
        echo '<p id="buddybot-chatbot-error-message" class="fw-bold">' . esc_html(__('Unable to save BuddyBot. Please fix errors.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</p>';
        echo '<ul id="buddybot-chatbot-errors-list" class="small"></ul>';
        echo '</div>';
    }

    private function chatbotShortcode()
    {
        if (!$this->is_edit) {
            return;
        }

        echo '<div class="mb-4">';
        echo '<span class="fw-bold">';
        esc_html_e('Shortcode', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo ': </span>';
        echo '<code>';
        echo '[buddybot_chat id=' . absint($this->chatbot_id) . ']';
        echo '</code>';
        echo '</div>';
    }

    private function chatbotOptions()
    {
        echo '<table class="form-table" role="presentation"><tbody>';
        $this->chatbotName();
        $this->chatbotDescription();
        $this->chatbotAssistant();
        echo '</tbody></table>';
    }

    private function getValue($name) {
        if ($this->is_edit) {
            return $this->chatbot->$name;
        } else {
            return '';
        }
    }

    private function chatbotName()
    {
        $value = $this->getValue('chatbot_name');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Name (required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</label>';
        echo '</th>';
        echo '<td>';
        echo '<input type="text" id="mgao-chatbot-name" value="' . esc_html($value) . '" class="buddybot-item-field regular-text">';
        echo '<p class="description">';
        esc_html_e('Name of your BuddyBot. This is not visible to the user.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function chatbotDescription()
    {
        $value = $this->getValue('chatbot_description');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-description">' . esc_html(__('Description', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</label></th>';
        echo '<td>';
        echo '<textarea name="moderation_keys" rows="10" cols="50" id="mgao-chatbot-description" class="buddybot-item-field">';
        echo esc_textarea($value);
        echo '</textarea>';
        echo '<p class="description">';
        esc_html_e('Description for your BuddyBot. This is not visible to the user.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function chatbotAssistant()
    {
        $value = $this->getValue('assistant_id');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Connect Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '</label></th>';
        echo '<td>';
        echo '<div class="small fw-bold text-secondary" id="mgao-chatbot-selected-assistant-name"></div>';
        echo '<div class="small mb-2 text-secondary" id="mgao-chatbot-selected-assistant-id">' . esc_html($value) . '</div>';
        echo '<input type="hidden" id="mgao-chatbot-assistant-id" value="' . esc_attr($value) . '">';
        echo '<button type="button" class="buddybot-item-field btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#buddybot-select-assistant-modal">';
        esc_html_e('Select Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
        echo '</td>';
        echo '</tr>';
    }

    protected function saveBtn()
    {
        echo '<p class="submit">';


        if ($this->is_edit) {
            $label = esc_html(__('Update BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        } else {
            $label = esc_html(__('Save BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        }
        
        $this->loaderBtn('dark btn-sm', 'mgao-chatbot-save-btn', $label);
        echo '</p>';
    }
    
    public function getInlineJs()
    {    
        return '
        jQuery(document).ready(function($){
    
            loadFirstBuddyBot();
    
            function loadFirstBuddyBot() {
                if (' . absint($this->chatbot_id) . ' !==  ' . absint($this->first_id) . ' ) {
                    location.replace("' . esc_url(admin_url()) . 'admin.php?page=buddybot-chatbot&chatbot_id=' . absint($this->first_id) . '");
                }
            }
        })';
    }  
}