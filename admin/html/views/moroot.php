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
                      echo '<a href="https://wordpress.org/support/plugin/buddybot-ai-custom-ai-assistant-and-chat-agent/" type="button" class="button button-secondary" id="buddybot-plugin-feedback-deactivation" target="_blank">';
                        echo esc_html__('Get Support', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    protected function buddybotFooterBanner()
    {
        echo '<div class="buddybot-footer-banner">';
            echo '<div class="buddybot-docs-inner buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-p-2">';
                echo '<div class="buddybot-banner-icon">';
                    echo '<span> <svg xmlns="http://www.w3.org/2000/svg" height="100px" viewBox="0 -960 960 960" width="124px" fill="#1f1f1f"><path d="M160-360q-50 0-85-35t-35-85q0-50 35-85t85-35v-80q0-33 23.5-56.5T240-760h120q0-50 35-85t85-35q50 0 85 35t35 85h120q33 0 56.5 23.5T800-680v80q50 0 85 35t35 85q0 50-35 85t-85 35v160q0 33-23.5 56.5T720-120H240q-33 0-56.5-23.5T160-200v-160Zm200-80q25 0 42.5-17.5T420-500q0-25-17.5-42.5T360-560q-25 0-42.5 17.5T300-500q0 25 17.5 42.5T360-440Zm240 0q25 0 42.5-17.5T660-500q0-25-17.5-42.5T600-560q-25 0-42.5 17.5T540-500q0 25 17.5 42.5T600-440ZM320-280h320v-80H320v80Zm-80 80h480v-480H240v480Zm240-240Z"/></svg> </span>';
                echo '</div>';
                echo '<div class="buddybot-banner-text">';
                    echo '<div class="buddybot-banner-head buddybot-fw-bold buddybot-fs-6 buddybot-text-dark">';
                        echo esc_html__('Get Expert Help with AI Integration for Your Site', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                    echo '<ul class="buddybot-text-dark">';

                        echo '<li class="">';
                            echo '<span class="">';
                                echo '<img src="' . BUDDYBOT_PLUGIN_URL . 'admin/html/images/third-party/openai/bb-key-icon.svg" width="14px" alt="Tick">';
                            echo '</span>';
                            echo esc_html__('Struggling with AI integration? Let our team guide you to unlock the full potential of BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</li>';

                        echo '<li class="">';
                            echo '<span class="">';
                                echo '<img src="' . BUDDYBOT_PLUGIN_URL . 'admin/html/images/third-party/openai/bb-key-icon.svg" width="14px" alt="Tick">';
                            echo '</span>';
                            echo esc_html__('What We Offer: Personalized support to seamlessly integrate BuddyBot. AI customization to suit your site\'s needs. Expert guidance on training BuddyBot with your content.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</li>';

                        echo '<li class="">';
                            echo '<span class="">';
                                echo '<img src="' . BUDDYBOT_PLUGIN_URL . 'admin/html/images/third-party/openai/bb-key-icon.svg" width="14px" alt="Tick">';
                            echo '</span>';
                            echo esc_html__('BuddyBot extensions are FREE for early adopters!', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</li>';

                    echo '</ul>';
                echo '</div>';
                echo '<div class="">';
                    echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button button-primary" id="buddybot-plugin-feedback-deactivation" target="_blank">';
                        echo esc_html__('Contact Us Now!', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</a>';
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
