<?php
/**
 * Plugin Name:       Metagauss OpenAI
 * Description:       Plugin to connect WordPress with OpenAI.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      8.0
 * Author URI:        https://profiles.wordpress.org/metagauss/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       BuddyBot
 * Domain Path:       /languages
 * Network:           False
*/

namespace BuddyBot;

//exit if the file is accessed directly.
if (!defined('WPINC')) die;

require_once(ABSPATH . 'wp-admin/includes/plugin.php');

function fileNotFound($file) {
    \deactivate_plugins(plugin_basename(__FILE__));
    wp_die();
}

if (is_readable(plugin_dir_path(__FILE__) . 'loader.php')) {
    require_once plugin_dir_path(__FILE__) . 'loader.php';
} else {
    fileNotFound(plugin_dir_path(__FILE__) . 'loader.php');
}

spl_autoload_register(array(__NAMESPACE__ . '\Loader', 'loadClass'));


//----------Admin Code--------//

if (is_admin()) {
    // include_once(plugin_dir_path(__FILE__) . '/Admin/adminmenu.php');
    $admin_menu = new Admin\AdminMenu();
    $admin_stylesheets = new Admin\StyleSheets();
    $admin_responses = new Admin\Responses\ChatBot();
    $admin_responses = new Admin\Responses\OrgFiles();
    $admin_responses = new Admin\Responses\AddFile();
    $admin_responses = new Admin\Responses\DataSync();
    $admin_responses = new Admin\Responses\Assistants();
    $admin_responses = new Admin\Responses\EditAssistant();
    $admin_responses = new Admin\Responses\Playground();
    $admin_responses = new Admin\Responses\Settings();
}

//----------Public Code--------//

if (!is_admin()) {
}

$buddybot_db = new MoDb();
register_activation_hook(__FILE__, array($buddybot_db, 'installPlugin'));