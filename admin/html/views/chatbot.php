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

    protected function setContext()
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
            location.replace("' . esc_url($location) . '");
            </script>
            ';
        }
    }

    public function getHtml()
    {
        $this->useSingleChatbot();
        $this->pageHeading($this->heading);
        $this->chatbotOptions();
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
        $value = $this->getValue('name');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Name', 'metagauss-openai')) . '</label></th>';
        echo '<td><input type="text" id="mgao-chatbot-name" value="' . esc_html($value) . '" class="regular-text"></td>';
        echo '</tr>';
    }

    private function chatbotDescription()
    {
        $value = $this->getValue('description');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Description', 'metagauss-openai')) . '</label></th>';
        echo '<td>';
        echo '<textarea name="moderation_keys" rows="10" cols="50" id="moderation_keys" class="">';
        echo esc_textarea($value);
        echo '</textarea>';
        echo '</td>';
        echo '</tr>';
    }

    private function chatbotAssistant()
    {
        $value = $this->getValue('assistant');

        echo '<tr>';
        echo '<th scope="row">';
        echo '<label for="mgao-chatbot-name">' . esc_html(__('Description', 'metagauss-openai')) . '</label></th>';
        echo '<td>';
        echo '<button type="button" class="button button-secondary">';
        echo __('Connect Assistant', 'metagauss-openai');
        echo '</button>';
        echo '</td>';
        echo '</tr>';
    }
    
}