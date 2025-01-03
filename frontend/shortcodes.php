<?php
namespace BuddyBot\Frontend;

final class ShortCodes extends \BuddyBot\Frontend\MoRoot
{
    protected $shortcodes;
    protected $frontend_theme;

    protected function setShortcodes()
    {
        $this->shortcodes = array(
            'buddybot_chat'
        );
    }

    protected function setFrontendTheme()
    {
        $this->frontend_theme = 'bootstrap';
    }

    public function addShortCodes()
    {
        foreach ($this->shortcodes as $shortcode) {
            $class = str_replace('_', '', $shortcode);

            $view_class = 'BuddyBot\Frontend\Views\\' . $this->frontend_theme . '\\' . $class;
            $view = $view_class::getInstance();
            add_shortcode($shortcode, array($view, 'shortcodeHtml'));
        }
    }

    public function buddybotEnqueueScript()
    {
        foreach ($this->shortcodes as $shortcode) {
            $class = str_replace('_', '', $shortcode);

            $this->enqueuePluginStyle();
            $this->enqueuePluginScript();
            $this->enqueueViewStyle($class);
        }
    }

    private function enqueuePluginStyle()
    {
        wp_enqueue_style(
            'buddybot-material-symbols',
            $this->config->getRootUrl() . 'external/material-symbols/material-symbols.css',
            array(),
            BUDDYBOT_PLUGIN_VERSION
        );

        switch ($this->frontend_theme) {
            case 'bootstrap':
                wp_enqueue_style(
                    'buddybot-bootstrap-style',
                    $this->config->getRootUrl() . 'external/bootstrap/bootstrap.min.css',
                    array(),
                    '5.3'
                );
                break;
        }
    }

    private function enqueuePluginScript()
    {
        wp_enqueue_script('jquery');

        switch ($this->frontend_theme) {
            case 'bootstrap':
                wp_enqueue_script(
                    'buddybot-bootstrap-script',
                    $this->config->getRootUrl() . 'external/bootstrap/bootstrap.min.js',
                    array(),
                    '5.3'
                );
                break;
        }
    }

    private function enqueueViewStyle($file)
    {
        $file_path = $this->config->getRootPath() . 'frontend/css/' . $file . '.css';
        
        if (file_exists($file_path)) {
            $file_url = $this->config->getRootUrl() . 'frontend/css/' . $file . '.css';
            wp_enqueue_style('buddybot-style-' . $file, $file_url, array(), BUDDYBOT_PLUGIN_VERSION);
        }

        $file_path = $this->config->getRootPath() . 'frontend/css/' . $this->frontend_theme . '/' . $file . '.css';
        if (file_exists($file_path)) {
            $file_url = $this->config->getRootUrl() . 'frontend/css/'  . $this->frontend_theme . '/' . $file . '.css';
            wp_enqueue_style('buddybot-style-' . $this->frontend_theme . '-' . $file, $file_url, array(), BUDDYBOT_PLUGIN_VERSION);
        }
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_enqueue_scripts', array($this, 'buddybotEnqueueScript'));
        $this->addShortCodes();
    }
}