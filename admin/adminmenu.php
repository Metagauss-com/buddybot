<?php
namespace MetagaussOpenAI\Admin;

final class AdminMenu
{
    public function topLevelMenu()
    {
        $this->mainMenuItem();
    }

    public function mainMenuItem()
    {
        add_menu_page(
            'Metagauss',
            'Metagauss',
            'manage_options',
            'metagauss-slug',
            array($this, 'appMenuPage'),
            'dashicons-superhero',
            6
        );
    }

    public function appMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/app.php');
    }

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
    }
}