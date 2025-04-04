<?php
namespace BuddyBot\Blocks;

final class GutenbergBlocks extends \BuddyBot\Blocks\MoRoot
{
    protected $options;
    
    public function enqueueScripts()
    {
        wp_enqueue_script(
            'buddybot-gutenberg-blocks', 
            plugin_dir_url(__FILE__) . 'gutenbergblocks.js', 
            array(
                'wp-blocks',
                'wp-editor',
                'wp-i18n',
                'wp-element',
                'wp-components',
            ),
            BUDDYBOT_PLUGIN_VERSION,
            true 
        );
    }

    public function registerRestEndpoint()
    {
        register_rest_route(
            'buddybot/v1',
            '/buddybots',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'getBuddyBots'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'buddybot/v1',
            '/api-key-status',
            array(
                'methods' => 'GET',
                'callback' => function () {
                    return new \WP_REST_Response([
                        'apiKeyExists' => !empty($this->options->getOption('openai_api_key'))
                    ], 200);
                },
                'permission_callback' => '__return_true',
            )
        );
    }

    public function getBuddyBots($request)
    {
        $sql = new \BuddyBot\Blocks\Sql\GutenbergBlocks();
        $buddybots = $sql->getAllBuddybots();

        if ($buddybots === false) {
            global $wpdb;
            return new \WP_REST_Response(
                array('message' => esc_html__('Database query error: ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . $wpdb->last_error),
                500
            );
        }

        if (empty($buddybots)) {
            return new \WP_REST_Response([], 200);
        }

        return new \WP_REST_Response($buddybots, 200);
    }

    public function registerBlocksContent()
    {

        wp_register_style(
            'buddybot-bootstrap-style',
            BUDDYBOT_PLUGIN_URL . 'external/bootstrap/bootstrap.min.css',
            array(),
            '5.3'
        );

        wp_register_style(
            'buddybot-custom-style',
            BUDDYBOT_PLUGIN_URL . 'frontend/css/buddybotchat.css',
            array(),
            BUDDYBOT_PLUGIN_VERSION
        );

        wp_register_style(
            'buddybot-gutenberg-style',
            BUDDYBOT_PLUGIN_URL . 'blocks/gutenbergblocks.css',
            array(),
            BUDDYBOT_PLUGIN_VERSION
        );

    

        register_block_type('buddybot/chat', array(
            'editor_style'   => 'buddybot-gutenberg-style',
            'style'         => array('buddybot-bootstrap-style', 'buddybot-custom-style', 'buddybot-symbols-style'),
            'attributes' => array(
                'selectedBuddyBot' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'customClass' => array( 
                    'type'    => 'string',
                    'default' => '',
                ),
                'align' => array(
                    'type' => 'string',
                    'default' => 'center',
                ),
                'bbTimeZone' => array(
                    'type' => 'string',
                    'default' => '',
                ),
            ),
            'render_callback' => array($this, 'render_buddybot_chat_block'),
        ));
    }

    public function render_buddybot_chat_block($atts) {
        // Ensure attributes are properly set
        $buddybot_id = isset($atts['selectedBuddyBot']) ? absint($atts['selectedBuddyBot']) : 0;
        $timezone = isset($atts['bbTimeZone']) ? sanitize_text_field($atts['bbTimeZone']) : '';
        $custom_class = isset($atts['customClass']) ? sanitize_html_class($atts['customClass']) : '';
        $align = isset($atts['align']) ? sanitize_text_field($atts['align']) : '';
        
        if (defined('REST_REQUEST') && REST_REQUEST) {

            ob_start();

            $buddybot = new \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat('');
            echo '<div id="buddybot-block-conversation-wrapper">';
            echo '<div id="buddybot-chat-conversation-list-header" class="d-flex justify-content-start align-items-center">';
            echo '<div class="small fw-bold me-2">';
            echo __('Select Conversation or', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo '</div>';

            echo '<button id="buddybot-chat-conversation-start-new" type="button" class="btn btn-dark btn-sm px-3 rounded-2">';
            echo __('Start New', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo '</button>';
            echo '</div>';

            echo '<div id="buddybot-chat-conversation-list-wrapper">';
            echo $buddybot->conversationList($timezone);
            echo '</div>';
            echo '</div>';

            echo '<div id="buddybot-block-single-conversation-wrapper" class="d-none">';
            echo '</div>';

            return ob_get_clean();
        }elseif (!empty($buddybot_id)) {
            return '<div class="' . esc_attr($custom_class . ' ' . $align) . '">' .
            do_shortcode('[buddybot_chat id=' . $buddybot_id . ']') .
            '</div>';
        }
        
    }


    public function __construct()
    {
        $this->setAll();
        add_action('rest_api_init', array($this, 'registerRestEndpoint'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueueScripts'));
        add_action('init', array($this, 'registerBlocksContent'));
    }
}
