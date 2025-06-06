<?php
namespace BuddyBot\Admin;

use BuddyBot\Traits\Singleton;

final class StyleSheets extends \BuddyBot\Admin\MoRoot
{
    use Singleton;

    protected function isInternalPage()
    {
        if (!empty($_GET['page']) and strpos(sanitize_text_field($_GET['page']), 'buddybot') === 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function pluginLevelScripts() 
    {
        $bootstrap_css = $this->config->getRootUrl() . 'external/bootstrap/bootstrap.min.css';
        $bootstrap_js = $this->config->getRootUrl() . 'external/bootstrap/bootstrap.min.js';
        $material_symbols = $this->config->getRootUrl() . 'external/material-symbols/material-symbols.css';
        
        if ($this->isInternalPage()) {
            wp_enqueue_style($this->config::PREFIX . '-material-symbols-css', esc_url($material_symbols), array(), BUDDYBOT_PLUGIN_VERSION);
            wp_enqueue_style($this->config::PREFIX . '-bootstrap-css', esc_url($bootstrap_css), array(), '5.3');
            wp_enqueue_script($this->config::PREFIX . '-bootstrap-js', esc_url($bootstrap_js), array(), '5.3');
            wp_enqueue_script('jquery');
            wp_enqueue_style($this->config::PREFIX . '-global-css', $this->config->getRootUrl() . 'admin/css/buddybot.css', array(), BUDDYBOT_PLUGIN_VERSION);

            if (isset($_GET['page']) && in_array($_GET['page'], ['buddybot-chatbot', 'buddybot-conversations', 'buddybot-editchatbot','buddybot-chatbubble', 'buddybot-playground'])) {
                wp_dequeue_style('buddybot-bootstrap-css');
                wp_dequeue_script('buddybot-bootstrap-js');
            }

        }
    }

    protected function pageLevelScripts()
    {
        if ($this->isInternalPage()) {
            $css_file_name = str_replace('buddybot-','', sanitize_text_field($_GET['page']));
            $css_file_url = $this->config->getRootUrl() . 'admin/css/' . $css_file_name . '.css';
            $css_file_path = $this->config->getRootPath() . 'admin/css/' . $css_file_name . '.css';

            if (file_exists($css_file_path)) {
                wp_enqueue_style(sanitize_text_field($_GET['page']), sanitize_url($css_file_url), array(), BUDDYBOT_PLUGIN_VERSION);
            }
        }
    }

    public function adminStyleSheets()
    {
        $this->pluginLevelScripts();
        $this->pageLevelScripts();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('init', array($this, 'adminStyleSheets'));
    }
}