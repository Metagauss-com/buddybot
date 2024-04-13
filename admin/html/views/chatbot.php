<?php

namespace MetagaussOpenAI\Admin\Html\Views;

final class ChatBot extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    protected $chatbot_id = 0;
    protected $is_edit = false;
    protected $chatbot;
    protected $heading;

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
            $this->heading = __('Edit Chatbot', 'metagauss-openai');
        } else {
            $this->heading = __('New Chatbot', 'metagauss-openai');
        }
    }

    protected function useSingleChatbot()
    {
        if ($this->chatbot_id != 1) {
            $location = admin_url() . 'admin.php?page=metagaussopenai-chatbot&chatbot_id=1';
            echo '
            <script>
            location.replace("' . $location . '");
            </script>
            ';
        }
    }

    protected function pageModals()
    {
        $select_assistant = new \MetagaussOpenAI\Admin\Html\Modals\SelectAssistant();
        $select_assistant->getHtml();
    }

    public function getHtml()
    {
        // $this->useSingleChatbot();
        $this->pageModals();
        $this->pageErrors();
        $this->pageHeading($this->heading);
        $this->chatbotOptions();
        $this->saveBtn();
    }

    private function pageErrors()
    {
        echo '<div id="mgoa-chatbot-errors" class="notice notice-error settings-error mb-3 ms-0">';
        echo '<p id="mgoa-chatbot-error-message" class="fw-bold">' . __('Unable to save Chatbot. Please fix errors.', 'metagauss-openai') . '</p>';
        echo '<ul id="mgoa-chatbot-errors-list" class="small"></ul>';
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
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Name', 'metagauss-openai')) . '</label></th>';
        echo '<td><input type="text" id="mgao-chatbot-name" value="' . esc_html($value) . '" class="mo-item-field regular-text"></td>';
        echo '</tr>';
    }

    private function chatbotDescription()
    {
        $value = $this->getValue('chatbot_description');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-description">' . esc_html(__('Description', 'metagauss-openai')) . '</label></th>';
        echo '<td>';
        echo '<textarea name="moderation_keys" rows="10" cols="50" id="mgao-chatbot-description" class="mo-item-field">';
        echo esc_textarea($value);
        echo '</textarea>';
        echo '</td>';
        echo '</tr>';
    }

    private function chatbotAssistant()
    {
        $value = $this->getValue('assistant_id');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Connect Assistant', 'metagauss-openai')) . '</label></th>';
        echo '<td>';
        echo '<div class="small fw-bold text-secondary" id="mgao-chatbot-selected-assistant-name"></div>';
        echo '<div class="small mb-2 text-secondary" id="mgao-chatbot-selected-assistant-id">' . esc_html($value) . '</div>';
        echo '<input type="hidden" id="mgao-chatbot-assistant-id" value="' . esc_attr($value) . '">';
        echo '<button type="button" class="mo-item-field button button-secondary" data-bs-toggle="modal" data-bs-target="#mgoa-select-assistant-modal">';
        echo __('Select Assistant', 'metagauss-openai');
        echo '</button>';
        echo '</td>';
        echo '</tr>';
    }

    protected function saveBtn()
    {
        echo '<p class="submit">';


        if ($this->is_edit) {
            $label = __('Update Chatbot', 'metagauss-openai');
        } else {
            $label = __('Save Chatbot', 'metagauss-openai');
        }
        
        $this->loaderBtn('primary btn-sm', 'mgao-chatbot-save-btn', $label);
        echo '</p>';
    }
    
}