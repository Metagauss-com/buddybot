<?php
/**
 * Plugin Name:       BuddyBot AI - Custom AI Assistant and Chat Agent
 * Description:       Create and connect BuddyBot with AI Assistant, syncronize site data and publish on the frontend.
 * Version:           1.3.7.0
 * Requires at least: 6.2
 * Requires PHP:      7.3
 * Author URI:        https://profiles.wordpress.org/buddybot/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       buddybot-ai-custom-ai-assistant-and-chat-agent
 * Domain Path:       /languages
 * Network:           False
*/

namespace BuddyBot;

define( 'BUDDYBOT_PLUGIN_VERSION', '1.3.7.0' );
define( 'BUDDYBOT_DATABASE_VERSION', '1.1' );
define('BUDDYBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

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

register_activation_hook(__FILE__, function() {
    $stored_version = get_option('buddybot_db_version', '0.1');

    if ((float) $stored_version < (float) BUDDYBOT_DATABASE_VERSION) {
        $buddybot_db = new MoDb();
        $buddybot_db->installPlugin(); 
        update_option('buddybot_db_version', BUDDYBOT_DATABASE_VERSION);
    }
});

add_action('init', function() {
    $stored_version = get_option('buddybot_db_version', '0.1');

    if ((float) $stored_version < (float) BUDDYBOT_DATABASE_VERSION) {
        $buddybot_db = new MoDb();
        $buddybot_db->installPlugin(); 
        update_option('buddybot_db_version', BUDDYBOT_DATABASE_VERSION);
    }
});

//----------Blocks--------//
$buddybot_Gutenberg_blocks = new Blocks\GutenbergBlocks();

//----------Admin Code--------//

if (is_admin()) {
    $buddybot_admin_menu = new Admin\AdminMenu();
    $buddybot_admin_stylesheets = new Admin\StyleSheets();
    $buddybot_chatbot_responses = new Admin\Responses\ChatBot();
    $buddybot_chatbot_responses = new Admin\Responses\EditChatBot();
    $buddybot_playground_responses = new Admin\Responses\Playground();
    $buddybot_settings_responses = new Admin\Responses\Settings();
    $buddybot_vectorstore_responses = new Admin\Responses\VectorStore();
    $buddybot_conversations_responses = new Admin\Responses\Conversations();
    $buddybot_conversation_responses = new Admin\Responses\ViewConversation();
    $buddybot_plugin_feedback = new Admin\Responses\PluginFeedback();
}

//----------Public Code--------//

if (!is_admin()) {
    $buddybot_shortcodes = new Frontend\ShortCodes();
}

$buddybot_responses = new Frontend\Responses\BuddybotResponses();
$buddybot_sessions = new Frontend\Sessions();
