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

                echo '<div class="buddybot-banner-icon ">';
                    echo ' <svg width="70px" height="50px" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/"  style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
    <path d="M441.803,214.844C472.163,227.124 495.829,251.877 506.735,282.758C508.21,286.883 509.333,291.238 510.19,295.535C510.695,298.07 510.73,302.703 511.768,304.808C511.84,304.958 511.923,305.1 512,305.248L512,319.918C510.905,321.48 510.575,328.085 510.1,330.398C508.55,337.923 506.395,345.625 503.123,352.59C488.558,383.587 464.005,404.915 431.902,416.51C431.527,423.885 432.513,460.188 431.26,463.647C430.837,464.808 429.883,465.943 428.755,466.467C427.328,467.133 425.858,466.855 424.513,466.135C421.56,464.553 417.22,460.44 414.308,458.248L377.745,430.587C374,427.788 367.26,422.068 363.29,420.143C362.703,419.858 362.085,419.723 361.438,419.665C355.995,419.173 350.217,419.575 344.745,419.583L312.53,419.6C305.58,419.598 298.555,419.813 291.623,419.272C281.645,418.495 271.428,416.465 262.13,412.703C236.848,402.467 214.005,381.883 203.242,356.575C213.789,355.865 224.632,356.363 235.204,356.402C248.244,356.45 261.42,356.84 274.44,356.063C301.929,354.558 328.729,346.884 352.848,333.61C388.904,313.807 417.077,282.193 432.612,244.102C434.475,239.486 436.253,234.791 437.678,230.018C439.178,224.997 440.33,219.875 441.803,214.844Z" style="fill-rule:nonzero;"/>
    <path d="M0,172.284C0.059,172.157 0.121,172.031 0.177,171.903C1.271,169.415 1.858,161.366 2.464,158.126C3.548,152.623 4.984,147.195 6.764,141.876C19.773,102.72 50.398,71.57 88.135,55.496C102.523,49.44 117.694,45.443 133.198,43.625C141.795,42.618 150.599,42.947 159.241,42.957L196.863,42.978L243.946,43.026C255.085,43.016 266.508,42.538 277.592,43.735C291.89,45.279 306.277,48.557 319.623,53.951C357.628,69.312 385.878,96.176 401.928,133.999C412.683,159.351 414.77,189.257 408.23,215.959C406.523,222.925 404.073,229.573 401.383,236.213C390.773,262.393 369.828,285.678 346.145,300.785C338.81,305.465 331.105,309.285 323.1,312.663C292.01,325.783 269.65,324.06 237.079,324.337L206.263,324.473C201.825,324.488 197.323,324.725 192.894,324.61C185.554,329.545 178.655,335.445 171.66,340.878L135.451,368.663L125.033,376.712C123.272,378.07 121.5,379.648 119.581,380.763C118.454,381.418 117.509,381.73 116.2,381.43C114.927,381.14 113.332,380.155 112.641,379.02C111.47,377.09 112.135,327.648 112.068,321.055C75.459,312.385 40.321,287.05 20.529,255.04C13.47,243.621 7.721,230.339 4.29,217.36C3.296,213.6 2.539,209.758 1.834,205.934C1.389,203.518 1.051,196.7 0,194.962L0,172.284Z" style="fill-rule:nonzero;"/>
    <path d="M203.584,92.553C204.744,92.416 205.914,92.378 207.08,92.44C211.243,92.65 215.408,94.439 218.145,97.629C220.981,100.935 222.109,105.157 221.714,109.459C221.216,114.888 218.528,118.164 214.491,121.557C215.119,121.53 215.747,121.514 216.375,121.509L244.858,121.504C255.085,121.47 266.413,120.423 276.33,122.925C284.402,124.997 291.778,129.18 297.7,135.043C305.214,142.359 310.048,151.999 311.418,162.396C311.975,166.851 311.913,171.415 311.92,175.899L311.923,203.208C311.928,216.174 312.758,227.219 306.7,239.247C304.393,243.827 301.573,247.786 297.935,251.405C292.152,257.163 285.063,261.65 277.168,263.858C267.22,266.64 256.478,265.65 246.259,265.68L208.886,265.703C188.776,265.82 168.595,266.003 148.488,265.745C135.35,265.578 123.121,261.087 113.893,251.535C108.189,245.63 103.994,238.868 101.78,230.933C99.02,221.044 99.965,207.329 99.958,196.901C99.951,187.613 99.786,178.29 99.99,169.005C100.278,155.928 105.174,143.586 114.768,134.503C120.5,129.076 128.108,124.965 135.763,123.061C145.713,120.587 157.848,121.489 168.124,121.484C177.94,121.479 187.799,121.289 197.609,121.564C194.245,119.016 191.498,116.02 190.567,111.761C189.651,107.569 190.424,102.954 192.761,99.344C195.308,95.409 199.121,93.477 203.584,92.553Z" style="fill:rgb(254,254,254);fill-rule:nonzero;"/>
    <path d="M189.451,211.557C195.599,211.171 201.901,211.439 208.062,211.443C212.487,211.446 217.191,211.124 221.588,211.523C222.866,211.64 224.106,212.017 225.233,212.63C227.605,213.913 229.368,216.207 230.137,218.778C230.959,221.528 230.672,224.633 229.252,227.14C227.724,229.839 225.465,231.091 222.57,231.849C217.307,232.474 211.545,231.966 206.229,231.975C201.105,231.983 195.677,232.453 190.606,231.94C188.985,231.777 187.399,231.352 185.977,230.545C183.765,229.291 182.225,227.297 181.579,224.837C180.906,222.276 181.053,218.588 182.433,216.286C184,213.67 186.591,212.286 189.451,211.557Z" style="fill-rule:nonzero;"/>
    <path d="M250.975,171.333C255.71,170.734 260.535,172.178 264.36,174.97C269.145,178.464 272.12,183.982 272.99,189.79C273.82,195.335 272.313,200.852 268.983,205.341C265.588,209.917 260.9,212.577 255.308,213.43C250.5,213.927 245.97,212.759 242.027,209.959C237.291,206.596 234.24,201.595 233.309,195.872C232.351,189.985 233.86,184.155 237.391,179.361C240.674,174.903 245.531,172.141 250.975,171.333Z" style="fill-rule:nonzero;"/>
    <path d="M252.083,183.982C252.762,183.962 253.523,183.912 254.085,184.38C254.542,184.762 254.78,185.252 254.76,185.851C254.732,186.65 254.3,187.206 253.81,187.788C253.06,187.788 252.28,187.833 251.67,187.31C251.17,186.881 250.943,186.436 250.963,185.771C250.99,184.986 251.56,184.482 252.083,183.982Z" style="fill:rgb(254,254,254);fill-rule:nonzero;"/>
    <path d="M155.906,171.305C160.705,170.969 165.22,171.514 169.358,174.143C174.399,177.345 177.744,182.773 178.959,188.565C180.124,194.114 178.756,199.96 175.649,204.66C172.45,209.499 168.036,212.315 162.407,213.469C157.211,213.928 152.464,213.172 148.079,210.208C143.242,206.938 139.998,201.52 138.907,195.827C137.839,190.248 139.448,184.446 142.628,179.808C145.746,175.26 150.515,172.303 155.906,171.305Z" style="fill-rule:nonzero;"/>
    <path d="M158.22,184.246C158.959,184.265 159.65,184.292 160.272,184.75C160.749,185.101 161.032,185.541 161.077,186.14C161.139,186.957 160.579,187.575 160.104,188.164C160.038,188.183 159.972,188.207 159.904,188.221C159.19,188.372 158.402,188.333 157.827,187.834C157.305,187.381 157.183,186.794 157.201,186.133C157.224,185.33 157.693,184.793 158.22,184.246Z" style="fill:rgb(254,254,254);fill-rule:nonzero;"/>
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
