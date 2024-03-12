<?php
namespace MetagaussOpenAI\Admin;

final class AdminMenu extends \MetagaussOpenAI\Admin\MoRoot
{
    public function topLevelMenu()
    {
        $this->mainMenuItem();
        $this->orgFilesSubmenuItem();
        $this->assistantsSubmenuItem();
        $this->addFileSubmenuItem();
        $this->dataSyncSubmenuItem();
    }

    public function hiddenMenu()
    {
        $this->editAssistantSubmenuItem();
    }

    public function mainMenuItem()
    {
        add_menu_page(
            'Metagauss',
            'Metagauss',
            'manage_options',
            'metagaussopenai-chatbot',
            array($this, 'appMenuPage'),
            'dashicons-superhero',
            6
        );
    }

    public function orgFilesSubmenuItem()
    {
        add_submenu_page(
            'metagaussopenai-chatbot',
            __('Files', 'metgauss-openai'),
            __('Files', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-files',
            array($this, 'filesMenuPage'),
            1
        );
    }

    public function addFileSubmenuItem()
    {
        add_submenu_page(
            'metagaussopenai-chatbot',
            __('Add File', 'metgauss-openai'),
            __('Add File', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-addfile',
            array($this, 'addFileMenuPage'),
            1
        );
    }

    public function dataSyncSubmenuItem()
    {
        add_submenu_page(
            'metagaussopenai-chatbot',
            __('Data Sync', 'metgauss-openai'),
            __('Data Sync', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-datasync',
            array($this, 'dataSyncMenuPage'),
            1
        );
    }

    public function assistantsSubmenuItem()
    {
        add_submenu_page(
            'metagaussopenai-chatbot',
            __('Assistants', 'metgauss-openai'),
            __('Assistants', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-assistants',
            array($this, 'assistantsMenuPage'),
            1
        );
    }

    public function editAssistantSubmenuItem()
    {
        add_submenu_page(
            '',
            __('Edit Assistant', 'metgauss-openai'),
            __('Edit Assistant', 'metgauss-openai'),
            'manage_options',
            'metagaussopenai-assistant',
            array($this, 'EditAssistantMenuPage'),
            1
        );
    }

    public function appMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/chatbot.php');
    }

    public function filesMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/orgfiles.php');
    }

    public function addFileMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/addfile.php');
    }

    public function dataSyncMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/datasync.php');
    }

    public function assistantsMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/assistants.php');
    }

    public function editAssistantMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/editassistant.php');
    }

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
        add_action( 'admin_menu', array($this, 'hiddenMenu'));
    }
}