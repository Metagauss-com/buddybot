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
                        esc_html_e('How is going?', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                    echo '<div class="buddybot-banner-text">';
                        esc_html_e(' Welcome to BuddyBot! If you\'re just getting started or have questions, these resources can help.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                    echo '<div class="buddybot-docs-actions">';
                        echo '<a href="' . esc_url($link) . '" type="button" class="button button-primary" target="_blank">';
                            esc_html_e('View Documentation', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</a>';
                        echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button button-primary" target="_blank">';
                            esc_html_e('Starter Guide', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</a>';
                        echo '<a href="https://wordpress.org/support/plugin/buddybot-ai-custom-ai-assistant-and-chat-agent/" type="button" class="button button-secondary" target="_blank">';
                            esc_html_e('Get Support', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</a>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    protected function buddybotFooterBanner()
    {
         $img_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/banner-icon.png';
        echo '<div class="buddybot-footer-banner">';
            echo '<div class="buddybot-docs-inner  buddybot-justify-content-between buddybot-align-items-center buddybot-p-2">';

                echo '<div class="buddybot-banner-icon">';
                    echo '<img src="' . esc_url($img_url) . '" alt="BuddyBot" class="buddybot-banner-icon-img" />';
                echo '</div>';
              
                echo '<div class="buddybot-banner-text">';
                    echo '<div class="buddybot-banner-head buddybot-fw-bold buddybot-fs-6 buddybot-text-dark buddybot-mb-2">';
                        esc_html_e('Get Expert Help with AI Integration for Your Site', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                        echo '<div class="buddybot-text-dark buddybot-mb-4">';
                                esc_html_e('Struggling with AI? Our team is here to help you unlock the full potential of BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                        echo '</div>';
                    echo '<ul class="buddybot-text-dark buddybot-mb-0">';

                    echo '<div class="buddybot-text-dark buddybot-fw-bold buddybot-mb-2">';
                        esc_html_e('What We Offer:', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';

                      echo '<li class="buddybot-d-flex buddybot-align-item-center buddybot-gap-2 buddybot-mb-0">';
                          echo '<span>';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000" style="flex-shrink: 0;"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                           echo '</span>';
                         echo '<span>' . esc_html__('AI customization that fits your needs.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                       echo '</li>';

                        echo '<li class="buddybot-d-flex buddybot-align-item-center buddybot-gap-2 buddybot-mb-0">';
                              echo '<span>';
                              echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                                  echo '</span>';
                               echo '<span>' . esc_html__('Step-by-step guidance on training BuddyBot with your content.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                               
                            echo '</li>';

                        echo '<li class="buddybot-d-flex buddybot-align-item-center buddybot-gap-2 buddybot-mb-0">';
                           echo '<span>';
                           echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                             echo '</span>';
                            echo '<span class="buddybot-fw-bold">' . esc_html__('BuddyBot extensions are FREE for early adopters!', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                        echo '</li>';

                    echo '</ul>';
                echo '</div>';
                echo '<div>';
                    echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button banner-button-primary" target="_blank">';
                        esc_html_e('Contact Us Now!', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
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
