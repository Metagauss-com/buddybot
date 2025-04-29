<?php

namespace BuddyBot\Admin\Html\Views;

class MoRoot extends \BuddyBot\Admin\Html\MoRoot
{
    public $config;
    protected $sql;

    protected function setSql()
    {
        $class_name = (new \ReflectionClass($this))->getShortName();
        $this->config = \BuddyBot\MoConfig::getInstance();
        $file_path = $this->config->getRootPath() . 'admin/sql/' . strtolower($class_name) . '.php';

        if (file_exists($file_path)) {
            $class_name = '\BuddyBot\Admin\Sql\\' . $class_name;
            $this->sql = $class_name::getInstance(); 
        }
    }

    protected function alertContainer()
    {
        echo '<div id="buddybot-alert-container" class="notice-error buddybot-ms-0" style="display:none;">';
        echo '<p></p>';
        echo '</div>';
    }

    protected function moSpinner()
    {
        echo '<div class="buddybot-dataload-spinner spinner-border spinner-border-sm text-dark" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
        echo '</div>';
    }

    protected function toastContainer()
    {
        echo '<div id="buddybot-toast-container">';
        echo '<div class="buddybot-toast">';
        echo '<div class="buddybot-toast-content">';
        echo '<span class="toast-message"></span>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    protected function documentationContainer($link = '')
{
    echo '<div class="buddybot-docs-container buddybot-mb-3">';
        echo '<div class="buddybot-docs-inner  buddybot-d-flex buddybot-align-items-center buddybot-align-item-center buddybot-p-2">';
  
            echo '<div class="buddybot-docs-content">';
                echo '<div class="buddybot-banner-head buddybot-text-dark">';
                    echo esc_html__('How is going?', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                echo '</div>';
                echo '<div class="buddybot-banner-text">';
                    echo esc_html__(' Welcome to BuddyBot! If you\'re just getting started or have questions, these resources can help.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                echo '</div>';
                echo '<div class="buddybot-docs-actions">';
                    echo '<a href="' . esc_url($link) . '" type="button" class="button button-primary" id="buddybot-plugin-feedback-direct-deactivation" target="_blank">';
                        echo esc_html__('View Documentation', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</a>';
                    echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button button-primary" id="buddybot-plugin-feedback-deactivation" target="_blank">';
                        echo esc_html__('Starter Guide', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</a>';
                      echo '<a href="https://wordpress.org/support/plugin/buddybot-ai-custom-ai-assistant-and-chat-agent/" type="button" class="button button-secondary" id="buddybot-plugin-feedback-deactivation" target="_blank"">';
                        echo esc_html__('Get Support', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}

    protected function pageModals()
    {
        
    }

    public function pageJs()
    {
        $name = str_replace('buddybot-','', sanitize_text_field($_GET['page']));
        $js_file_url = $this->config->getRootUrl() . 'admin/js/' . $name . '.js';
        wp_enqueue_script(sanitize_text_field($_GET['page']), sanitize_url($js_file_url), array('jquery'), BUDDYBOT_PLUGIN_VERSION);

        if (method_exists($this, 'getInlineJs')) {
            wp_add_inline_script(sanitize_text_field($_GET['page']), $this->getInlineJs());
        }

        $requests_class = '\BuddyBot\Admin\Requests\\' . $name;
        $requests = new $requests_class();
        wp_add_inline_script(sanitize_text_field($_GET['page']), $requests->requestsJs());
    }
}