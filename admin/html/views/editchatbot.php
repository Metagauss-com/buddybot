<?php

namespace BuddyBot\Admin\Html\Views;

class EditChatBot extends \BuddyBot\Admin\Html\Views\MoRoot
{
    protected $sql;
    protected $buddybot_id = 0;
    protected $is_edit = false;
    protected $chatbot;
    protected $heading;

    protected function setBuddyBotId()
    {
        if (!empty($_GET['chatbot_id'])) {
            $this->buddybot_id = absint($_GET['chatbot_id']);
        }
    }

    protected function setIsEdit()
    {
        $chatbot = $this->sql->getItemById('chatbot', $this->buddybot_id);
        //var_dump($this->chatbot_id);

        if (is_object($chatbot)) {
            $this->is_edit = true;
            $this->chatbot = $chatbot;
        }
    }

    protected function setHeading()
    {
        if ($this->is_edit) {
            $this->heading = __('Edit BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->heading = __('New BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    }

    public function getHtml()
    {
        $this->customPageHeading($this->heading);
        $this->pageSuccessAlert();
        $this->alertContainer();
        $this->buddybotFieldsTable();
        $this->pageBtns();
        $this->pageModals();
    }

    private function pageSuccessAlert()
    {
        if (empty($_GET['success']) or absint($_GET['success']) != 1) {
            return;
        }

        echo '<div id="buddybot-settings-success" class="notice notice-success buddybot-mb-3 buddybot-ms-0 buddybot-py-2">';
        echo '<span id="buddybot-settings-success-message">' . esc_html('BuddyBot updated successfully.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
        echo '</div>';
    }

    protected function pageModals()
    {
        $deleteBuddybot = new \BuddyBot\Admin\Html\CustomModals\Instructions();
        $deleteBuddybot->getHtml();
    }

    private function customPageHeading($heading)
    {
        echo '<h1>';
        echo esc_html($heading);
        echo '</h1>';
    }

    private function buddybotFieldsTable()
    {
        echo '<table class="form-table" id="buddybot-table" role="presentation">';
        $this->tableBody();
        echo '</table>';
    }

    private function tableBody()
    {
        echo '<tbody>';
            $this->buddybotName();
            $this->buddybotDescription();
            $this->assistantName();
            $this->assistantModel();
            $this->additionalInstructions();
            $this->assistantTemperature();
            $this->assistantTopP();
            $this->openaiSearch();
            $this->openaiSearchMsg();
            $this->personalizedOptions();
            //$this->assistantFallbackBehavior();
            $this->emotionDetection();
            $this->greetingMessage();
            // $this->multilingualSupport();
            // $this->languageOptions();
        echo '</tbody>';
    }

    private function buddybotName()
    {
        $id = 'buddybot-buddybotname';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('BuddyBot Name (Required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<input name="' . esc_attr($id) . '" type="text" id="' . esc_attr($id) . '" value="" class="regular-text" maxlength="256" placeholder="' . esc_attr__('e.g., EventBot, Customer Support Bot', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        echo '<p class="description">' . esc_html__('This is how you will recognize your BuddyBot. Maximum 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function buddybotDescription()
    {
        $id = 'buddybot-buddybotdescription';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('BuddyBot Description', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<textarea name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" class="large-text" rows="5" maxlength="512" placeholder="' . esc_attr__('e.g., This BuddyBot handles event-related queries.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '"></textarea>';
        echo '<p class="description">' . esc_html__('This description will help you recall the purpose of your BuddyBot. Maximum 1024 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';

    }

    private function assistantName()
    {
        $id = 'buddybot-assistantname';
        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('Assistant Name (Friendly Name) (Required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<input name="' . esc_attr($id) . '" type="text" id="' . esc_attr($id) . '" value="" class="regular-text" maxlength="256" placeholder="' . esc_attr__('e.g., Max, Sarah, EventBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        echo '<p class="description">' . esc_html__('This is the name that users will see during interactions (e.g., Max, BuddyBot).', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function assistantModel()
    {
        $id = 'buddybot-assistantmodel';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Assistant Model (Required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<div class="buddybot-d-flex">';
        echo '<select id="' . esc_attr($id) . '" class="buddybot-item-field">';
        echo '<option value="" selected>' . esc_html__('Loading...', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '</select>';
        echo '<span id="buddybot-assistant-model-spinner" class="spinner is-active" aria-hidden="true"></span>';
        echo '</div>';
        echo '<p class="description">' . esc_html__('Select the AI model for your assistant. Advanced models like GPT-4 offer better accuracy and features.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function additionalInstructions()
    {
        $id = 'buddybot-additionalinstructions';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('Additional Instructions', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<textarea name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" class="large-text" rows="5" maxlength="512" placeholder="' . esc_attr__('e.g., Be polite and provide helpful responses regarding events and bookings.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '"></textarea>';
        echo '<p class="description">' . esc_html__('Provide instructions to guide the assistant\'s behavior (e.g., tone, manner of speech). Maximum 512 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '<a href="javascript:void(0)" id="buddybot-view-sample-btn" data-modal="buddybot-sample-instructions-modal">' . esc_html__('View Sample Instructions', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</a>';
        echo '</td>';
        echo '</tr>';
        
    }

    private function assistantTemperature()
    {
        $default_value = 1;
        $id = 'buddybot-assistanttemperature';
    
        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Response Creativity', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<input type="range" name="'. esc_attr($id) .'" id="'. esc_attr($id . '-range') .'" min="0.0" max="2.0" step="0.1" style="width:350px" value="' . esc_attr($default_value) . '" class="buddybot-item-field">';
        echo '<span id="'. esc_attr($id . '-value') . '" class="temperature-range-value">' . esc_html($default_value) . '</span>';
        echo '<p class="description">' . esc_html__('Adjusts the randomness of responses. Lower values provide more accurate answers, higher values make it more creative.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function assistantTopP()
    {
        $default_value = 1;
        $id = 'buddybot-assistanttopp';
    
        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Response Diversity', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<input type="range" name="'. esc_attr($id) .'" id="'. esc_attr($id . '-range') .'" min="0.0" max="1.0" step="0.1" style="width:350px" value="' . esc_attr($default_value) . '" class="buddybot-item-field">';
        echo '<span id="'. esc_attr($id . '-value') . '">' . esc_html($default_value) . '</span>';
        echo '<p class="description">' . esc_html__('Adjusts response diversity. Lower values make responses more focused, higher values increase variation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function openaiSearch()
    {
        $id = 'buddybot-openaisearch';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Disallow Assistant to Seek Answers from OpenAI', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<fieldset>';
        echo '<label for="' . esc_attr($id) . '">';
        echo '<input type="checkbox" name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" value="1">';
        echo esc_html__('Disallow Openai Search', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label>';
        echo '<p class="description">' . esc_html__('If enabled, the assistant will not query the main OpenAI model (e.g., GPT-4) if it cannot find an answer in the vector store.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
    }

    private function openaiSearchMsg()
    {
        $id = 'buddybot-openaisearch-msg';

        echo '<tr id="buddybot-openaisearch-childfieldrow" style="display: none;">';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('Fallback Msg', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<textarea name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" class="large-text" rows="5" maxlength="512" placeholder="' . esc_attr__('e.g., Be polite and provide helpful responses regarding events and bookings.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '"></textarea>';
        echo '<p class="description">' . esc_html__('Provide instructions to guide the assistant\'s behavior (e.g., tone, manner of speech).  Maximum 512 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function personalizedOptions()
    {
        $id = 'buddybot-personalizedoptions';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Personalized Responses', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<fieldset>';
        echo '<label for="' . esc_attr($id) . '">';
        echo '<input type="checkbox" name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" value="1" checked>';
        echo esc_html__('Enable Personalized Responses', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label>';
        echo '<p class="description">' . esc_html__('Enable the assistant to use the logged-in user\'s name and bio for personalized responses.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
    }

    private function assistantFallbackBehavior()
    {
        $id = 'buddybot-fallbackbehavior';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Fallback Behavior (Required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<select name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" class="buddybot-item-field">';
        echo '<option value="ask">' . esc_html__('Ask for clarification', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '<option value="generic">' . esc_html__('Provide a generic response', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '<option value="escalate">' . esc_html__('Escalate to support', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '</select>';
        echo '<p class="description">' . esc_html__('Choose what the assistant should do when it doesn\'t know the answer (e.g., ask for clarification, escalate to support).', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function emotionDetection()
    {
        $id = 'buddybot-emotiondetection';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Emotion Detection', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<fieldset>';
        echo '<label for="' . esc_attr($id) . '">';
        echo '<input type="checkbox" name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" value="1">';
        echo esc_html__(' Enable Emotion Detection', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label>';
        echo '<p class="description">' . esc_html__('Enable the assistant to detect and respond to the user\'s emotional tone.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
    }

    private function greetingMessage()
    {
        $id = 'buddybot-greetingmessage';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('Greeting Message', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<input name="' . esc_attr($id) . '" type="text" id="' . esc_attr($id) . '" value="" class="regular-text" maxlength="256" placeholder="' . esc_attr__('e.g., Hi! How can I help you today?', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        echo '<p class="description">' . esc_html__('Customize the message shown to users when they start a conversation with your BuddyBot. Maximum 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</td>';
        echo '</tr>';
    }

    private function multilingualSupport()
    {
        $id = 'buddybot-multilingualsupport';

        echo '<tr>';
        echo '<th scope="row">' . esc_html__('Multilingual Support', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</th>';
        echo '<td>';
        echo '<fieldset>';
        echo '<label for="' . esc_attr($id) . '">';
        echo '<input type="checkbox" name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" value="1">';
        echo esc_html__(' Enable multilingual support', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label>';
        echo '<p class="description">' . esc_html__('Enable multilingual support for the assistant to handle conversations in different languages.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</fieldset>';
        echo '</td>';
        echo '</tr>';
    }

    private function pageBtns()
    {
        if ($this->is_edit) {
            $btn_label = __('Update BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $btn_label = __('Save BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        $id = 'buddybot-buddybotsubmit';

        echo '<p class="buddybot-btn-wrap submit">';

        echo '<input type="submit" id="' . esc_attr($id) . '" ';
        echo 'class="button button-primary" value="' . esc_html($btn_label) . '">';

        echo ' <a href="' . esc_url(admin_url('admin.php?page=buddybot-chatbot')) . '" class="button">';
        echo esc_html__('Back', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</a>';
        echo '<span class="spinner is-active" style="display:none;" aria-hidden="true"></span>';

        echo '</p>';

    }   
}