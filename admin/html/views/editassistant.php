<?php

namespace MetagaussOpenAI\Admin\Html\Views;

class EditAssistant extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
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
            $this->heading = __('Edit Assistant');
        } else {
            $this->heading = __('New Assistant');
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
        echo '<div class="mgoa-container row w-75 rounded border small">';
        
        echo '<div class="col-md-8 border-end p-5">';
        $this->assistantName();
        $this->assistantDescription();
        $this->assistantModel();
        $this->assistantInstructions();
        $this->assistantTools();
        echo '</div>';

        echo '<div class="col-md-4 p-0 flex-column">';
        $this->orgFiles();
        echo '</div>';
        
        echo '<div class="col-md-12 p-3 border-top">';
        $this->submitBtn();
        echo '</div>';
        
        echo '</div>';
    }

    private function assistantName()
    {
        $id = 'mo-editassistant-assistantname';
        $placeholder = __('Example, Math Tutor', 'metagauss-openai');
        $label = __('Name', 'metagauss-openai');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<input type="text" class="w-100 mo-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" size="256">';
        echo '<p class="description">' . esc_html__('Optional. Maximum 256 characters.', 'metagauss-openai') . '</p>';
        echo '</div>';
    }

    private function assistantDescription()
    {
        $id = 'mo-editassistant-assistantdescription';
        $placeholder = __('Description of your assistant', 'metagauss-openai');
        $label = __('Description', 'metagauss-openai');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 mo-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5" maxlength="512"></textarea>';
        echo '<p class="description">' . esc_html__('Optional. Maximum 512 characters.', 'metagauss-openai') . '</p>';
        echo '</div>';
    }

    private function assistantModel()
    {
        $id = 'mo-editassistant-assistantmodel';
        $label = __('Assistant Model', 'metagauss-openai');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<div><select id="' . esc_attr($id) . '" class="me-2 mo-item-field">';
        echo '<option value="" selected>' . esc_html__('Loading...', 'metagauss-openai') . '</option>';
        echo '</select>';
        $this->moSpinner();
        echo '</div>';
        echo '<p class="description">' . esc_html__('Required. AI Model for this Assistant.', 'metagauss-openai') . '</p>';
        echo '</div>';
    }

    private function assistantInstructions()
    {
        $id = 'mo-editassistant-assistantinstructions';
        $placeholder = __('Example, You are a personal math tutor. When asked a question, write and run Python code to answer the question.', 'metagauss-openai');
        $label = __('Instructions', 'metagauss-openai');
        echo '<div class="mb-4">';
        echo '<label for="' . esc_attr($id) . '" class="form-label fw-bold">' . esc_html($label) . '</label>';
        echo '<textarea class="w-100 mo-item-field" id="' . esc_attr($id) . '" placeholder="' . esc_attr($placeholder) . '" rows="5" maxlength="32768"></textarea>';
        echo '<p class="description">' . esc_html__('Optional. Maximum 32768 characters.', 'metagauss-openai') . '</p>';
        echo '</div>';
    }

    private function assistantTools()
    {
        $id = 'mo-editassistant-assistanttools';
        $label = __('Tools', 'metagauss-openai');
        echo '<div id="' . esc_attr($id) . '" class="mb-4">';
        echo '<div class="form-label fw-bold">' . esc_html($label) . '</div>';
        
        echo '<div><label for="' . esc_attr($id . '-code') . '">';
        echo '<input type="checkbox" id="' . esc_attr($id . '-code') . '" value="code_interpreter" class="mo-item-field">';
        echo esc_html__('Code Interpreter', 'metagauss-openai');
        echo '</label></div>';

        echo '<div><label for="' . esc_attr($id . '-retrieval') . '">';
        echo '<input type="checkbox" id="' . esc_attr($id . '-retrieval') . '" value="retrieval" class="mo-item-field">';
        echo esc_html__('Retrieval', 'metagauss-openai');
        echo '</label></div>';
        
        echo '<p class="description">' . esc_html__('Optional. The tools enabled on the assistant.', 'metagauss-openai') . '</p>';
        echo '</div>';
    }

    private function orgFiles()
    {
        $id = 'mo-editassistant-assistantfiles';
        
        echo '<div class="p-3 border-bottom">';
        
        echo '<div class="fw-bold">';
        echo esc_html__('Attached Files', 'metagauss-openai');
        echo '<span class="text-success ms-1 fw-normal" id="' . esc_attr($id . '-filescount') . '">';
        echo '</span>';
        echo '</div>';
        
        echo '<p class="description">' . esc_html__('Optional. Select files to be attached to this Assistant. Maximum 20 files (not more than 512MB each) allowed.', 'metagauss-openai') . '</p>';
        
        echo '</div>';
        
        echo '<div id="' . esc_attr($id) . '" class="p-3 small" style="height:100%;overflow:auto;">';
        echo '<div class="mt-5 text-center">';
        $this->moSpinner();
        echo '</div>';
        echo '</div>';
    }

    private function submitBtn()
    {
        if ($this->assistant_id !== null) {
            $btn_label = __('Update Assistant');
        } else {
            $btn_label = __('Save Assistant');
        }

        $id = 'mo-editassistant-editassistant-submit';
        echo '<div>';
        
        $this->loaderBtn('success', $id, $btn_label);

        echo '</div>';
    }
    
}