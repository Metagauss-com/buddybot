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
        echo '<div id="buddybot-alert-container" class="notice notice-error mb-2 ms-0" style="display:none;">';
        echo '<p></p>';
        echo '</div>';
    }

    protected function moSpinner()
    {
        echo '<div class="buddybot-dataload-spinner spinner-border spinner-border-sm text-dark" role="status">';
        echo '<span class="visually-hidden">Loading...</span>';
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