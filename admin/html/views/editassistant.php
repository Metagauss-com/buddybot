<?php

namespace BuddyBot\Admin\Html\Views;

class EditAssistant extends \BuddyBot\Admin\Html\Views\MoRoot
{
    protected $assistant_id = null;
    protected $heading;

    protected function setAssistantId()
    {
        if (!empty($_GET['assistant_id'])) {
            $this->assistant_id = sanitize_text_field($_GET['assistant_id']);
        }
    }

    protected function setHeading()
    {
        if ($this->assistant_id !== null) {
            $this->heading = __('Edit Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->heading = __('New Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }
    }

    public function getHtml()
    {
        $this->customPageHeading($this->heading);
        $this->alertContainer();
        $this->buddybotFieldsTable();
        //$this->assistantFields();
    }

    private function customPageHeading($heading)
    {
        //echo '<div class="">';
        echo '<h1>';
        echo esc_html($heading);
        echo '</h1>';
        //echo '</div>';
    }

    private function buddybotFieldsTable()
    {
        echo '<table class="form-table" role="presentation">';
        $this->tableBody();
        echo '</table>';
    }

    private function tableBody()
    {
        echo '<tbody>';
            $this->buddybotName();
            $this->buddybotDescription();
        echo '</tbody>';
    }

    private function buddybotName()
    {
        $id = 'buddybot-buddybotname';

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($id) . '">' . esc_html__('BuddyBot Name (Required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</label></th>';
        echo '<td>';
        echo '<input name="' . esc_attr($id) . '" type="text" id="' . esc_attr($id) . '" value="" class="regular-text" maxlength="256" placeholder="' . esc_attr__('e.g., Customer Support Bot, Order Tracking Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
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
        echo '<textarea name="' . esc_attr($id) . '" id="' . esc_attr($id) . '" class="large-text" rows="6" maxlength="512" placeholder="' . esc_attr__('Describe your assistant’s role and functionality...', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '"></textarea>';
        echo '<p class="description">' . esc_html__('This description will help you recall the purpose of your BuddyBot. Maximum 1024 characters.', 'your-text-domain') . '</p>';
        echo '</td>';
        echo '</tr>';

    }

    private function assistantFields()
    {
        echo '<div class="buddybot-container row w-75 small">';

        echo '<div class="col-md-8 pe-3">';
        $this->assistantName();
        $this->assistantDescription();
        $this->assistantModel();
        $this->assistantInstructions();
        //$this->assistantTools();
        $this->assistantTemperature();
        $this->assistantTop_P();
        $this->fallbackBehavior();
        echo '</div>';

        // echo '<div class="col-md-4 p-0 flex-column bg-light rounded-3 small overflow-hidden mt-4" style="max-height: 700px;">';
        // $this->orgFiles();
        // echo '</div>';

        echo '<div class="col-md-12 p-3">';
        $this->backBtn();
        $this->submitBtn();
        echo '</div>';

        echo '</div>';
    }

    private function assistantName()
    {
        $id = 'buddybot-editassistant-assistantname';
        $placeholder = __('e.g., Customer Support Bot, Order Tracking Assistant, Appointment Scheduler, Event Info Helper', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Assistant Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<input type="text" class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" size="256">';
        echo '<p class="description text-dark">' . esc_html__('Add a name to identify your assistant. Maximum 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantDescription()
    {
        $id = 'buddybot-editassistant-assistantdescription';
        $placeholder = __('e.g., Helps customers with queries, Tracks orders and updates delivery status', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Assistant Description', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5" maxlength="512"></textarea>';
        echo '<p class="description text-dark">' . esc_html__("This is how you will recognize your BuddyBot. Maximum 256 characters.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantModel()
    {
        $id = 'buddybot-editassistant-assistantmodel';
        $label = __('Assistant Model (Required)', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<div><select id="' . esc_attr($id) . '" class="small me-2 buddybot-item-field">';
        echo '<option value="" selected>' . esc_html__('Loading...', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '</select>';
        $this->moSpinner();
        echo '</div>';
        echo '<p class="description text-dark">' . esc_html__('Select the AI model for your assistant. Advanced models like GPT-4 offer better accuracy and features.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantInstructions()
    {
        $this->assistantNameInstructions();
        //$this->assistantGreetingsInstructions();
        $this->assistantAdditionalInstructions();
    }

    private function assistantNameInstructions()
    {
        $id = 'buddybot-editassistant-nameinstruction';
        $placeholder = __('e.g., Sarah, Max, BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Friendly Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<input type="text" class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '">';
        echo '<p class="description text-dark">' . esc_html__('Define a user-friendly name for the assistant. This name will be used during chat sessions.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantGreetingsInstructions()
    {
        $id = 'buddybot-editassistant-assistantgreeting';
        $placeholder = __('e.g., Hi there! How can I assist you today?', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Initial Greeting', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5"></textarea>';
        echo '<p class="description text-dark">' . esc_html__("Set the greeting message the assistant will use to welcome users in new chat threads.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantAdditionalInstructions()
    {
        $id = 'buddybot-editassistant-aditionalinstructions';
        $placeholder = __('e.g., You are a customer support assistant. Respond to queries about services and pricing.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Additional Instructions', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5"></textarea>';
        echo '<p class="description text-dark">' . esc_html__("Provide detailed instructions to guide the assistant's behavior. Maximum 32,768 characters.", 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';   
    }

    private function assistantTools()
    {
        $id = 'buddybot-editassistant-assistanttools';
        $label = __('Tools', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div id="' . esc_attr($id) . '" class="mb-4">';
        echo '<div class="form-label fw-bold">' . esc_html($label) . '</div>';

        echo '<div><label for="' . esc_attr($id . '-code') . '">';
        echo '<input type="checkbox" id="' . esc_attr($id . '-code') . '" value="code_interpreter" class="buddybot-item-field">';
        echo esc_html__('Code Interpreter', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label></div>';

        echo '<div><label for="' . esc_attr($id . '-file') . '">';
        echo '<input type="checkbox" id="' . esc_attr($id . '-file') . '" value="file_search" class="buddybot-item-field">';
        echo esc_html__('FileSearch', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label></div>';

        echo '<p class="description text-dark">' . esc_html__('The tools enabled on the assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantTemperature()
    {

        $default_value = 1;
        $id = 'buddybot-editassistant-assistanttemperature';
        $label = __('Response Temperature', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4 w-50">';

        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<input type="range" class="form-range buddybot-item-field" min="0.0" max="2.0" step="0.1" id="'. esc_attr($id . '-range') .'" value="' . esc_attr($default_value) . '">';
        echo '<span id="'. esc_attr($id . '-value') . '" class="temperature-range-value buddybot-item-field">0</span>';
        echo '<p class="description text-dark">' . esc_html__('Adjusts response randomness. Lower values provide more focused and accurate answers, while higher values produce creative responses.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantTop_P()
    {

        $default_value = 1;
        $id = 'buddybot-editassistant-assistanttopp';
        $label = __('Top-p (Nucleus Sampling)', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4 w-50">';

        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<input type="range" class="form-range buddybot-item-field" min="0.0" max="1.0" step="0.1" id="'. esc_attr($id . '-range') .'" value="' . esc_attr($default_value) . '">';
        echo '<span id="'. esc_attr($id . '-value') . '" class="topp-range-value buddybot-item-field">0</span>';
        echo '<p class="description text-dark">' . esc_html__('Adjusts response diversity. Lower values result in more focused answers, while higher values increase response variability.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }
    
    private function fallbackBehavior() {
        $id = 'buddybot-fallback-behavior';
    
        $label = __('Fallback Behavior', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<div><select id="' . esc_attr($id) . '" class="small me-2 buddybot-item-field">';
        echo '<option value="ask">' . esc_html__('Ask for clarification', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '<option value="generic">' . esc_html__('Provide a generic response', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '<option value="escalate">' . esc_html__('Escalate to support', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '</select>';
        echo '</div>';
        echo '<p class="description text-dark">' . esc_html__('Choose how the assistant should respond when it doesn\'t know the answer.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function submitBtn()
    {
        if ($this->assistant_id !== null) {
            $btn_label = __('Update Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $btn_label = __('Save Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        $id = 'buddybot-editassistant-editassistant-submit';
        
        $this->loaderBtn('dark btn-sm', $id, $btn_label);

        echo '</div>';

    }

    private function backBtn()
    {
        echo '<div>';
        $id = 'buddybot-editassistant-back';
        $class = 'btn btn-dark btn-sm';
        echo '<button id="' . esc_attr($id) . '" class="' . esc_attr($class) . ' me-2" type="button">';
        
        echo '<span class="buddybot-backbtn-label">';
        echo esc_html__('Back', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</span>';

        //echo '<span class="buddybot-loaderbtn-spinner spinner-border spinner-border-sm visually-hidden" aria-hidden="true"></span>';
        
        echo '</button>';
    }
    
}