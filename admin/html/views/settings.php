<?php

namespace MetagaussOpenAI\Admin\Html\Views;

final class Settings extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    protected $sections;

    protected function setSections()
    {
        $this->sections = array(
            'general' => __('General', 'metagauss-openai'),
            'extensions' => __('Extensions', 'metagauss-openai')
        );
    }

    public function getHtml()
    {
        $heading = __('Settings', 'megaform-openai');
        $this->pageHeading($heading);
        $this->sectionToggle();
        $this->optionsLoader();
        $this->sectionOptions();
        $this->updateOptions();
    }

    private function sectionToggle()
    {
        echo '<label for="mgao-settings-section-select" class="small my-3">';
        echo esc_html(__('Select', 'metagauss-openai'));
        $this->sectionSelect();
        echo '</label>';
    }

    private function sectionSelect()
    {
        echo '<select id="mgao-settings-section-select" class="ms-2">';

        foreach ($this->sections as $name => $label)
        {
            echo '<option value="' . esc_html($name) . '">';
            echo esc_html($label);
            echo '</label>';
        }

        echo '</select>';
    }

    private function sectionOptions()
    {
        echo '<table id="mgoa-settings-section-options" class="form-table mt-3" role="presentation"><tbody>';
        echo '</tbody></table>';
    }

    private function optionsLoader()
    {
        echo '<div id="mgoa-settings-section-options-loader" class="text-center mt-5 visually-hidden">';

        echo '<span>';
        esc_html_e('Loading options...', 'metagauss-openai');
        echo '</span>';

        echo '<div class="spinner-border spinner-border-sm ms-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>';

        echo '</div>';
    }

    private function updateOptions()
    {
        echo '<p class="submit">';
        echo '<input type="submit" id="mgoa-settings-update-btn" ';
        echo 'class="button button-primary" value="' . __('Save Options', 'metagauss-openai') . '">';
        echo '</p>';
    }
}