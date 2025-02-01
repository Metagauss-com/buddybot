<?php

namespace BuddyBot\Admin\Html\Views;

final class Settings extends \BuddyBot\Admin\Html\Views\MoRoot
{
    protected $sections;

    protected function setSections()
    {
        $this->sections = array(
            'general' => esc_html__('General', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
            // 'extensions' => esc_html__('Extensions', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
        );
    }

    public function getHtml()
    {
        $this->pageSuccessAlert();
        $this->pageErrors();

        $heading = esc_html__('Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->pageHeading($heading);
        $this->pageModals();
        $this->sectionToggle();
        $this->optionsLoader();
        $this->sectionOptions();
        $this->updateOptions();
    }

    private function pageSuccessAlert()
    {
        if (empty($_GET['success']) or absint($_GET['success']) != 1) {
            return;
        }

        echo '<div id="buddybot-settings-success" class="notice notice-success mb-3 ms-0 py-2 small">';
        echo '<span id="buddybot-settings-success-message" class="fw-medium">' . esc_html('Settings updated successfully.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
        echo '</div>';
    }

    protected function pageModals()
    {
        $select_assistant = new \BuddyBot\Admin\Html\Modals\ChangeKeyConfirmation();
        $select_assistant->getHtml();
    }

    private function pageErrors()
    {
        echo '<div id="buddybot-settings-errors" class="notice notice-error settings-error mb-3 ms-0" style="display:none;">';
        echo '<p id="buddybot-settings-error-message" class="fw-bold">' . esc_html__('Unable to update settings. Please fix errors.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        echo '<ul id="buddybot-settings-errors-list" class="small mb-0 mx-0 px-0"></ul>';
        echo '</div>';
    }

    private function sectionToggle()
    {
        echo '<label for="mgao-settings-section-select" class="small my-3 visually-hidden">';
        echo esc_html(__('Select', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        $this->sectionSelect();
        echo '</label>';
    }

    private function sectionSelect()
    {
        echo '<select id="mgao-settings-section-select" class="ms-2">';

        foreach ($this->sections as $name => $label)
        {
            $selected = '';

            if (!empty($_GET['section']) and sanitize_text_field($_GET['section']) === $name) {
                $selected = ' selected';
            }

            echo '<option value="' . esc_attr($name) . '"' . esc_attr($selected) . '>';
            echo esc_html($label);
            echo '</label>';
        }

        echo '</select>';
    }

    private function sectionOptions()
    {
        echo '<table id="buddybot-settings-section-options" class="form-table mt-3" role="presentation"><tbody>';
        echo '</tbody></table>';
    }

    private function optionsLoader()
    {
        echo '<div id="buddybot-settings-section-options-loader" class="text-center mt-5 visually-hidden">';

        echo '<span>';
        esc_html_e('Loading options...', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</span>';

        echo '<div class="spinner-border spinner-border-sm ms-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>';

        echo '</div>';
    }

    private function updateOptions()
    {
        echo '<div class="submit bb-settings-submit">';
        echo '<input type="submit" id="buddybot-settings-update-btn" ';
        echo 'class="button button-primary" value="' . esc_html__('Save Options', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        echo '<span class="spinner is-active" style="display:none;" aria-hidden="true"></span>';
        echo '</div>';
    }
}