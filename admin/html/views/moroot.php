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
                    echo '<svg width="100%" height="100%" viewBox="0 0 720 697" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
    <g transform="matrix(1,0,0,1,-155.43,-116.658)">
        <path d="M552.07,812.32C539.77,812.99 527.96,813.43 516.13,813.25C489.83,812.86 463.53,812.3 437.32,810.18C398.97,807.14 360.98,801.79 324.76,787.96C265.3,765.26 228.91,722.02 212.85,661.04C211.4,655.54 210.1,652.33 203.29,651.71C177.68,649.39 161.53,630.11 157.29,609.63C156.035,602.58 155.413,595.431 155.43,588.27L155.43,505.87C155.65,478.54 172.89,456.26 196.74,450.27C200.42,449.35 204.27,448.67 208.05,448.69C211.99,448.71 213.6,447.13 214.56,443.48C220.38,421.51 229.76,401.22 243.58,383.12C270.12,348.23 305.82,327.63 347.64,316.62C370.38,310.64 393.54,307.43 416.93,305.44C437.66,303.68 458.39,302.44 479.21,302.45C490.14,302.45 490.57,301.46 491.47,290.68L495.94,245.08C496.74,237.82 497.46,230.56 498.37,223.32C499.04,218.02 499.73,212.69 500.47,207.38C501.04,203.01 499.5,200.42 495.7,198.08C477.67,186.85 470.04,164.85 476.89,144.87C482.97,127.18 498.09,116.48 516.79,116.66C533.36,116.83 548.65,128.72 554.12,145.7C560.83,166.53 553.13,187.43 534.34,198.75C531.71,200.33 529.79,201.75 530.17,205.24L535.24,253.88C536.07,261.96 536.86,270.04 537.43,278.14C537.72,282.44 539.09,284.48 543.95,284.9C558.9,286.2 573.9,285.55 588.85,286.63C627.01,289.46 665.27,291.92 701.49,305.87C752.91,325.6 790.73,359.43 809.92,412.12C811.79,417.27 813.28,422.54 814.74,427.85C815.52,430.69 816.9,431.84 820,431.82C850.85,431.61 874.18,456.84 874.45,487.9C874.69,514.89 874.63,541.89 874.48,568.89C874.29,602.49 849.16,622.66 826.73,623.62C821.25,623.85 818.44,626.19 817.12,632.03C812.36,653.12 804.52,673.05 792.69,691.29C772.83,721.94 745.15,743.03 712.08,757.6C684.72,769.65 655.82,775.8 626.34,779.49C602.56,782.44 578.69,784.21 554.24,784.53L552.07,812.32ZM580.29,426.32C572.63,426.28 564.98,425.54 557.35,425.49C506.92,425.15 456.46,423.72 406.2,429.3C384.84,431.67 363.72,435.04 343.51,442.66C317.8,452.36 299.42,469.6 289.98,495.69C283.83,512.7 281.27,530.5 280.49,548.45C279.63,568.41 280.81,588.39 284.85,608.06C289.12,628.9 297.1,647.75 312.97,662.61C327.58,676.3 345.64,682.97 364.53,687.54C402.72,696.8 441.82,698.45 480.83,699.46C525.41,700.62 570.01,700.32 614.46,695.87C635.97,693.67 657.3,690.49 677.95,683.72C711.13,672.8 733.29,651.14 741.81,616.84C749.28,586.76 749.56,556.26 744.96,525.78C742.46,509.25 737.79,493.29 728.6,478.98C714.58,457.14 693.77,445.22 669.54,438.29C640.87,430.07 611.34,428.23 580.84,426.38L580.29,426.32Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,-155.43,-116.658)">
        <path d="M619.59,507.2C651.13,501.88 669.95,522.3 675.15,547.82C678.5,564.24 676.41,580.26 667.8,595C650.06,625.28 607.69,624.48 590.98,595C577.25,570.76 580.64,535.67 599.61,517.67C605.17,512.4 611.64,508.85 619.61,507.2L619.59,507.2Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,-155.43,-116.658)">
        <path d="M445.31,554.1C446.28,568.56 444.66,581.93 437.57,594.51C421.92,622.37 385.01,625.68 365.16,600.75C347.52,578.6 347.96,542.33 366.16,520.6C381.99,501.65 414.77,501.53 430.83,520.6C439.05,530.25 443.8,541.4 445.31,554.1Z" style="fill-rule:nonzero;"/>
    </g>
</svg>';
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