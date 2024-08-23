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
        $jquery_js = $this->config->getRootUrl() . 'external/jquery/jquery-3.7.1.min.js';
        $material_symbols = $this->config->getRootUrl() . 'external/material-symbols/material-symbols.css';
        
        if ($this->isInternalPage()) {
            wp_enqueue_style($this->config::PREFIX . '-material-symbols-css', esc_url($material_symbols));
            wp_enqueue_style($this->config::PREFIX . '-bootstrap-css', esc_url($bootstrap_css));
            wp_enqueue_script($this->config::PREFIX . '-bootstrap-js', esc_url($bootstrap_js));
            wp_enqueue_script($this->config::PREFIX . '-jquery-js', esc_url($jquery_js));
            wp_enqueue_style($this->config::PREFIX . '-global-css', $this->config->getRootUrl() . 'admin/css/buddybot.css');

        }
    }

    protected function pageLevelScripts()
    {
        if ($this->isInternalPage()) {
            $css_file_name = str_replace('buddybot-','', sanitize_text_field($_GET['page']));
            $css_file_url = $this->config->getRootUrl() . 'admin/css/' . $css_file_name . '.css';
            wp_enqueue_style(sanitize_text_field($_GET['page']), sanitize_url($css_file_url));
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