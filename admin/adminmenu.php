<?php
namespace MetagaussOpenAI\Admin;

final class AdminMenu
{
    public function topLevelMenu()
    {
        $this->appMenuItem();
    }

    private function appMenuItem()
    {
        add_menu_page(
            __( 'Custom Menu Title', 'textdomain' ),
            'custom menu',
            'manage_options',
            'myplugin/myplugin-admin.php',
            '',
            plugins_url( 'myplugin/images/icon.png' ),
            6
        );
    }

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
    }
}