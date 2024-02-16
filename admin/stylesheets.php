<?php
namespace MetagaussOpenAI\Admin;

use MetagaussOpenAI\Traits\Singleton;

final class StyleSheets extends \MetagaussOpenAI\Admin\MoRoot
{
    use Singleton;

    protected function isInternalPage()
    {
        if (key_exists('page', $_GET) and strpos($_GET['page'], 'metagaussopenai') == 0) {
            return true;
        } else {
            return false;
        }
    }

    protected function pluginLevelScripts() 
    {
        $bootstrap_css = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css';
        $bootstrap_js = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js';
        $jquery_js = 'https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js';
        
        if ($this->isInternalPage()) {
            wp_enqueue_style($this->config::PREFIX . '-bootstrap-css', $bootstrap_css);
            wp_enqueue_script($this->config::PREFIX . '-bootstrap-js', $bootstrap_js);
            wp_enqueue_script($this->config::PREFIX . '-jquery-js', $jquery_js);
        }
    }

    public function adminStyleSheets()
    {
        $this->pluginLevelScripts();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('init', array($this, 'adminStyleSheets'));
    }
}