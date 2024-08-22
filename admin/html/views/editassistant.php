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
        $this->pageHeading($this->heading);
        $this->alertContainer();
        $this->assistantFields();
    }

    private function assistantFields()
    {
        echo '<div class="buddybot-container row w-75 small">';
        
        echo '<div class="col-md-8 pe-3">';
        $this->assistantName();
        $this->assistantDescription();
        $this->assistantModel();
        $this->assistantInstructions();
        $this->assistantTools();
        echo '</div>';

        echo '<div class="col-md-4 p-0 flex-column bg-light rounded-3 small overflow-hidden mt-4" style="max-height: 700px;">';
        $this->orgFiles();
        echo '</div>';
        
        echo '<div class="col-md-12 p-3">';
        $this->submitBtn();
        echo '</div>';
        
        echo '</div>';
    }

    private function assistantName()
    {
        $id = 'buddybot-editassistant-assistantname';
        $placeholder = __('Example, Math Tutor', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Name', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<input type="text" class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" size="256">';
        echo '<p class="description text-dark">' . esc_html__('Optional. Maximum 256 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantDescription()
    {
        $id = 'buddybot-editassistant-assistantdescription';
        $placeholder = __('Description of your assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Description', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5" maxlength="512"></textarea>';
        echo '<p class="description text-dark">' . esc_html__('Optional. Maximum 512 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantModel()
    {
        $id = 'buddybot-editassistant-assistantmodel';
        $label = __('Assistant Model', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<div><select id="' . esc_attr($id) . '" class="small me-2 buddybot-item-field">';
        echo '<option value="" selected>' . esc_html__('Loading...', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        echo '</select>';
        $this->moSpinner();
        echo '</div>';
        echo '<p class="description text-dark">' . esc_html__('Required. AI Model for this Assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function assistantInstructions()
    {
        $id = 'buddybot-editassistant-assistantinstructions';
        $placeholder = __('Example, You are a personal math tutor. When asked a question, write and run Python code to answer the question.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $label = __('Instructions', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 buddybot-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5" maxlength="32768"></textarea>';
        echo '<p class="description text-dark">' . esc_html__('Optional. Maximum 32768 characters.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
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

        echo '<div><label for="' . esc_attr($id . '-retrieval') . '">';
        echo '<input type="checkbox" id="' . esc_attr($id . '-retrieval') . '" value="retrieval" class="buddybot-item-field">';
        echo esc_html__('Retrieval', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</label></div>';
        
        echo '<p class="description text-dark">' . esc_html__('Optional. The tools enabled on the assistant.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '</div>';
    }

    private function orgFiles()
    {
        $id = 'buddybot-editassistant-assistantfiles';
        
        echo '<div class="p-3 mb-4 bg-secondary bg-opacity-10">';
        
        echo '<div class="fw-bold text-uppercase mb-3 small">';
        echo esc_html__('Files Selected', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<span class="ms-1 fw-normal font-monospace" id="' . esc_attr($id . '-filescount') . '">';
        echo '</span>';
        echo '</div>';
        
        echo '<p class="small text-dark">' . esc_html__(
            'Optional. Select files to be attached to this Assistant. Maximum 20 files (not more than 512MB each) allowed. Requires RETRIEVAL tool.', 'buddybot'
            ) . '</p>';
        
        echo '</div>';
        
        echo '<div id="' . esc_attr($id) . '" class="ps-3 small" style="height:500px;overflow:auto;">';
        echo '<div class="mt-5 text-center">';
        $this->moSpinner();
        echo '</div>';
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
        echo '<div>';
        
        $this->loaderBtn('dark btn-sm', $id, $btn_label);

        echo '</div>';
    }
    
}