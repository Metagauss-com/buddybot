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
        echo '</div>';
    }

    protected function buddybotFooterBanner()
    {
        echo '<div class="buddybot-footer-banner">';
            echo '<div class="buddybot-docs-inner  buddybot-justify-content-between buddybot-align-items-center buddybot-p-2">';

                echo '<div class="buddybot-banner-icon">';
                    echo ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024">
  <path fill="#000" d="M552.07 812.32c-12.3.67-24.11 1.11-35.94.93-26.3-.39-52.6-.95-78.81-3.07-38.35-3.04-76.34-8.39-112.56-22.22-59.46-22.7-95.85-65.94-111.91-126.92-1.45-5.5-2.75-8.71-9.56-9.33-25.61-2.32-41.76-21.6-46-42.08a120.3 120.3 0 0 1-1.86-21.36v-82.4c.22-27.33 17.46-49.61 41.31-55.6 3.68-.92 7.53-1.6 11.31-1.58 3.94.02 5.55-1.56 6.51-5.21 5.82-21.97 15.2-42.26 29.02-60.36 26.54-34.89 62.24-55.49 104.06-66.5 22.74-5.98 45.9-9.19 69.29-11.18 20.73-1.76 41.46-3 62.28-2.99 10.93 0 11.36-.99 12.26-11.77l4.47-45.6c.8-7.26 1.52-14.52 2.43-21.76.67-5.3 1.36-10.63 2.1-15.94.57-4.37-.97-6.96-4.77-9.3-18.03-11.23-25.66-33.23-18.81-53.21 6.08-17.69 21.2-28.39 39.9-28.21 16.57.17 31.86 12.06 37.33 29.04 6.71 20.83-.99 41.73-19.78 53.05-2.63 1.58-4.55 3-4.17 6.49l5.07 48.64c.83 8.08 1.62 16.16 2.19 24.26.29 4.3 1.66 6.34 6.52 6.76 14.95 1.3 29.95.65 44.9 1.73 38.16 2.83 76.42 5.29 112.64 19.24 51.42 19.73 89.24 53.56 108.43 106.25 1.87 5.15 3.36 10.42 4.82 15.73.78 2.84 2.16 3.99 5.26 3.97 30.85-.21 54.18 25.02 54.45 56.08.24 26.99.18 53.99.03 80.99-.19 33.6-25.32 53.77-47.75 54.73-5.48.23-8.29 2.57-9.61 8.41-4.76 21.09-12.6 41.02-24.43 59.26-19.86 30.65-47.54 51.74-80.61 66.31-27.36 12.05-56.26 18.2-85.74 21.89-23.78 2.95-47.65 4.72-72.1 5.04zm28.22-386c-7.66-.04-15.31-.78-22.94-.83-50.43-.34-100.89-1.77-151.15 3.81-21.36 2.37-42.48 5.74-62.69 13.36-25.71 9.7-44.09 26.94-53.53 53.03-6.15 17.01-8.71 34.81-9.49 52.76-.86 19.96.32 39.94 4.36 59.61 4.27 20.84 12.25 39.69 28.12 54.55 14.61 13.69 32.67 20.36 51.56 24.93 38.19 9.26 77.29 10.91 116.3 11.92 44.58 1.16 89.18.86 133.63-3.59 21.51-2.2 42.84-5.38 63.49-12.15 33.18-10.92 55.34-32.58 63.86-66.88 7.47-30.08 7.75-60.58 3.15-91.06-2.5-16.53-7.17-32.49-16.36-46.8-14.02-21.84-34.83-33.76-59.06-40.69-28.67-8.22-58.2-10.06-88.7-11.91z"/>
  <path fill="#000" d="M619.59 507.2c31.54-5.32 50.36 15.1 55.56 40.62 3.35 16.42 1.26 32.44-7.35 47.18-17.74 30.28-60.11 29.48-76.82 0-13.73-24.24-10.34-59.33 8.63-77.33 5.56-5.27 12.03-8.82 20-10.47z"/>
  <path fill="#000" d="M445.31 554.1c.97 14.46-.65 27.83-7.74 40.41-15.65 27.86-52.56 31.17-72.41 6.24-17.64-22.15-17.2-58.42 1-80.15 15.83-18.95 48.61-19.07 64.67 0 8.22 9.65 12.97 20.8 14.48 33.5z"/>
</svg>
';
echo '</div>';
              
                echo '<div class="buddybot-banner-text">';
                    echo '<div class="buddybot-banner-head buddybot-fw-bold buddybot-fs-6 buddybot-text-dark">';
                        echo esc_html__('Get Expert Help with AI Integration for Your Site', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
                    echo '</div>';
                    echo '<ul class="buddybot-text-dark">';

                      echo '<li style="display: flex; align-items: center; gap: 8px;">';
                          echo '<span style="display: flex; align-items: center;">';
                            echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000" style="flex-shrink: 0;"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                           echo '</span>';
                         echo '<span>' . esc_html__(' Struggling with AI integration? Let our team guide you to unlock the full potential of BuddyBot.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                       echo '</li>';

                        echo '<li style="display: flex; align-items: flex-start; gap: 8px; margin-bottom: 8px;">';
                              echo '<span style="flex-shrink: 0;">';
                              echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                                  echo '</span>';
                               echo '<span>' . esc_html__('What We Offer : Personalized support to seamlessly integrate BuddyBot. AI customization to suit your site\'s needs. Expert guidance on training BuddyBot with your content.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                               
                            echo '</li>';

                        echo '<li style="display: flex; align-items: flex-start; gap: 8px;">';
                           echo '<span style="flex-shrink: 0;">';
                           echo '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#000000"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg>';
                             echo '</span>';
                            echo '<span>' . esc_html__('BuddyBot extensions are FREE for early adopters!', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</span>';
                        echo '</li>';

                    echo '</ul>';
                echo '</div>';
                echo '<div class="">';
                    echo '<a href="https://getbuddybot.com/starter-guide/" type="button" class="button banner-button-primary" id="buddybot-plugin-feedback-deactivation" target="_blank">';
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