<?php
namespace BuddyBot\Admin;

final class AdminMenu extends \BuddyBot\Admin\MoRoot
{
    protected $icon;

    public function topLevelMenu()
    {
        $this->mainMenuItem();
        $this->playgroundSubmenuItem();
        $this->settingsSubmenuItem();
        $this->vectorStoreSubmenuItem();
        $this->conversationsSubmenuItem();
    }

    public function hiddenMenu()
    {
        $this->editChatBotSubmenuItem();
        $this->defaultBuddyBotWizard();
        $this->ViewConversationsSubmenuItem();
    }

    public function mainMenuItem()
    {
        $icon = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="100%" viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
<path fill="#000000" opacity="1.000000" stroke="none" 
	d="
M552.066284,812.318970 
	C539.770569,812.990234 527.960693,813.426880 516.129700,813.251587 
	C489.833221,812.862122 463.531403,812.298279 437.315399,810.219360 
	C398.963318,807.178162 360.977417,801.834351 324.757751,788.008301 
	C265.295441,765.309570 228.903610,722.058899 212.849655,661.083313 
	C211.401245,655.582031 210.096268,652.374756 203.290466,651.756775 
	C177.681717,649.431580 161.530365,630.137878 157.295029,609.633423 
	C156.458221,605.582275 155.824020,601.405579 155.803177,597.283203 
	C155.664886,569.952942 155.534378,542.620117 155.764740,515.291199 
	C155.980286,489.718262 173.217575,467.441437 197.069168,461.445587 
	C200.749969,460.520325 204.607162,459.841492 208.379028,459.859009 
	C212.324860,459.877411 213.938858,458.297089 214.903259,454.654327 
	C220.720245,432.682770 230.109756,412.381927 243.915985,394.238525 
	C270.463348,359.351471 306.157013,338.766022 347.977509,327.759186 
	C370.711670,321.775757 393.869751,318.577423 417.260132,316.590454 
	C437.994354,314.829163 458.729431,313.603210 479.546692,313.611328 
	C490.475067,313.615570 490.898010,312.591644 491.799927,301.805603 
	C492.961975,287.909180 494.751740,274.065216 496.275940,260.199188 
	C497.074280,252.936356 497.778839,245.661026 498.725800,238.417511 
	C499.297150,234.047073 497.644653,231.475662 493.866669,229.124863 
	C475.835693,217.905273 468.210297,195.911041 475.065308,175.932297 
	C481.139191,158.230042 496.277985,147.516815 514.953247,147.704895 
	C531.520874,147.871735 546.816711,159.764877 552.288513,176.734528 
	C558.997437,197.540878 551.257568,218.440063 532.476379,229.762329 
	C529.837952,231.352905 527.914062,232.803513 528.299255,236.279358 
	C529.979919,251.448654 531.576416,266.627625 533.123596,281.811218 
	C533.948853,289.910126 534.738770,298.016907 535.295776,306.137177 
	C535.590515,310.434570 536.947876,312.480438 541.812805,312.903381 
	C556.756775,314.202423 571.749084,313.546265 586.692688,314.650696 
	C624.844910,317.470398 663.083923,319.924225 699.290894,333.809814 
	C750.711548,353.529846 788.526855,387.369720 807.726074,440.056580 
	C809.602600,445.206207 811.091492,450.506653 812.550842,455.794891 
	C813.333496,458.631012 814.719604,459.778168 817.808533,459.756622 
	C848.651611,459.541595 871.976746,484.657318 872.258118,515.702148 
	C872.502869,542.697754 872.435974,569.697815 872.284912,596.694702 
	C872.096985,630.289429 846.982422,650.471558 824.564392,651.423645 
	C819.078735,651.656616 816.264709,654.001709 814.952881,659.825500 
	C810.201782,680.918396 802.368347,700.849304 790.538757,719.091736 
	C770.670776,749.730225 742.975647,770.833191 709.909180,785.403931 
	C682.549133,797.460144 653.650818,803.616394 624.170349,807.277466 
	C600.401062,810.229248 576.523071,811.999084 552.066284,812.318970 
M580.285767,426.318054 
	C572.627502,426.281372 564.973511,425.500519 557.348328,425.449036 
	C506.923309,425.108551 456.460144,423.648865 406.177612,429.240082 
	C384.817871,431.615234 363.686615,435.028015 343.469147,442.658417 
	C317.747864,452.366028 299.380127,469.624329 289.943909,495.716797 
	C283.785309,512.746155 281.213318,530.550964 280.437805,548.513733 
	C279.576385,568.466736 280.269409,588.414368 284.305145,608.100891 
	C288.572388,628.916565 296.544952,647.772949 312.394623,662.656006 
	C326.996674,676.367615 345.033264,683.026184 363.936096,687.606018 
	C402.140198,696.862061 441.254486,698.485107 480.260437,699.447998 
	C524.833984,700.548340 569.458679,700.238220 613.923889,695.754456 
	C635.453003,693.583496 656.779358,690.396179 677.414734,683.595337 
	C710.590881,672.661316 732.723938,651.001526 741.242554,616.707947 
	C748.707703,586.655090 748.992554,556.179382 744.381348,525.702881 
	C741.881165,509.179352 737.212036,493.220123 728.033997,478.917053 
	C714.023682,457.083435 693.200928,445.183929 668.958191,438.243317 
	C640.294373,430.037048 610.763489,428.191986 580.285767,426.318054 
z"/>
<path fill="#000000" opacity="1.000000" stroke="none" 
	d="
M619.585266,507.197449 
	C651.120544,501.876526 669.937134,522.325500 675.137329,547.845764 
	C678.486328,564.281189 676.402405,580.314514 667.766479,595.059570 
	C650.020935,625.358154 607.667480,624.580078 590.958313,595.064941 
	C577.236267,570.826172 580.627808,535.732971 599.614929,517.733643 
	C605.177002,512.460938 611.656128,508.910583 619.585266,507.197449 
z"/>
<path fill="#000000" opacity="1.000000" stroke="none" 
	d="
M445.310333,554.100464 
	C446.276672,568.559570 444.643127,581.922302 437.564056,594.521667 
	C421.915131,622.373718 385.004761,625.671326 365.147461,600.723816 
	C347.508850,578.563782 347.938232,542.303589 366.096405,520.578857 
	C381.927124,501.638702 414.689636,501.522125 430.726196,520.327087 
	C438.951019,529.971863 443.700165,541.118225 445.310333,554.100464 
z"/>
</svg>
';
$base64_icon = base64_encode($icon);

        add_menu_page(
            'BuddyBot',
            'BuddyBot',
            'manage_options',
            'buddybot-chatbot',
            array($this, 'appMenuPage'),
            'data:image/svg+xml;base64,' . $base64_icon,
            6
        );
    }

    public function playgroundSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('Test Area', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Test Area', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-playground',
            array($this, 'playgroundMenuPage'),
            3
        );
    }

    public function wizardSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('Wizard', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Wizard', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-wizard',
            array($this, 'wizardMenuPage'),
            1
        );
    }

    public function orgFilesSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('Files', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Files', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-files',
            array($this, 'filesMenuPage'),
            1
        );
    }

    public function addFileSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('Add File', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Add File', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-addfile',
            array($this, 'addFileMenuPage'),
            1
        );
    }

    public function editchatBotSubmenuItem()
    {
        add_submenu_page(
            'buddybot-hidden-page',
            esc_html__('Edit BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Edit BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-editchatbot',
            array($this, 'EditChatBotMenuPage'),
            1
        );
    }

    public function defaultBuddyBotWizard()
    {
        add_submenu_page(
            'buddybot-hidden-page',
            esc_html__('Default Buddybot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Default Buddybot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-defaultwizard',
            array($this, 'defaultBuddybotWizardMenuPage'),
            1
        );
    }

    public function settingsSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-settings',
            array($this, 'settingsMenuPage'),
            6
        );
    }

    public function vectorStoreSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('AI Training', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('AI Training', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-vectorstore',
            array($this, 'vectorStoreMenuPage'),
            1
        );
    }

    public function conversationsSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            esc_html__('Conversation', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('Conversation', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-conversations',
            array($this, 'conversationsMenuPage'),
            2
        );
    }

    public function ViewConversationsSubmenuItem()
    {
        add_submenu_page(
            'buddybot-hidden-page',
            esc_html__('View Conversations', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            esc_html__('View Conversations', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-viewconversation',
            array($this, 'ViewConversationMenuPage'),
            1
        );
    }

    public function buddybotsMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/buddybots.php');
    }

    public function appMenuPage()
    {
        //include_once(plugin_dir_path(__FILE__) . 'pages/buddybots.php');
        include_once(plugin_dir_path(__FILE__) . 'pages/chatbot.php');
    }

    public function filesMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/orgfiles.php');
    }

    public function playgroundMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/playground.php');
    }

    public function wizardMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/wizard.php');
    }

    public function addFileMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/addfile.php');
    }

    public function EditChatBotMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/editchatbot.php');
    }

    public function defaultBuddyBotWizardMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/defaultbuddybotwizard.php');
    }

    public function settingsMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/settings.php');
    }

    public function vectorStoreMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/vectorstore.php');
    }

    public function conversationsMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/conversations.php');
    }

    public function ViewConversationMenuPage()
    {
        include_once(plugin_dir_path(__FILE__) . 'pages/viewconversation.php');
    }

    public function plugindeactivationFeedback()
    {
        if ( get_current_screen()->parent_base == 'plugins' ) {
            wp_enqueue_style('pluginfeedback', plugin_dir_url(__FILE__) . 'css/pluginfeedback.css', array(), BUDDYBOT_PLUGIN_VERSION);
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'pluginfeedback', plugin_dir_url( __FILE__ ) . 'js/pluginfeedback.js', array( 'jquery' ),BUDDYBOT_PLUGIN_VERSION, true );

            wp_localize_script(
                'pluginfeedback',
                'buddybot_feedback',
                array(
                    'ajaxurl'        => admin_url( 'admin-ajax.php' ),
                    'empty'   => esc_html__( 'Please provide feedback or check the temporary deactivation option.', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ),
                    'deactivation' => esc_html__( 'Deactivating BuddyBot...', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ),
                    'nonce' => wp_create_nonce( 'buddybot_plugin_deactivation' ),
                )
            );

            $plugin_feedback = new \BuddyBot\Admin\Html\Views\PluginFeedback();
            $plugin_feedback->getHtml();
        }
    }

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
        add_action( 'admin_menu', array($this, 'hiddenMenu'));
        // add_action( 'wp_ajax_bb_dismissible_notice', array($this,'bb_dismissible_notice_ajax') );
		// add_action( 'admin_notices',array($this,'bb_dismissible_notice') );
        add_action( 'admin_enqueue_scripts', array($this,'enqueue_scripts' ));
        add_action( 'admin_footer', array($this,'plugindeactivationFeedback' ));
 
    }

    public function enqueue_scripts() 
    {
       wp_enqueue_style('buddybotbanner', plugin_dir_url(__FILE__) . 'css/global.css', array(), BUDDYBOT_PLUGIN_VERSION);
        wp_enqueue_script( 'jquery' );
       // wp_enqueue_script('wp-notices');
        wp_enqueue_script( 'buddybotbanner', plugin_dir_url( __FILE__ ) . 'js/buddybotbanner.js', array( 'jquery' ),BUDDYBOT_PLUGIN_VERSION, true );
        wp_localize_script(
            'buddybotbanner',
            'bb_ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'ajax-nonce' ),
                'bb_dismissed_modal' => get_option( 'buddybot_welcome_modal_dismissed', false )
            )
        );
    }

    // public function buddybotActivationModel()
    // {
    //     $welcomeModal = new \BuddyBot\Admin\Html\CustomModals\Welcome();
    //     $welcomeModal->getHtml();
    // }

    // public function bb_dismissible_notice()
    // {
        
	// 	$notice_name = get_option( 'buddybot_welcome_modal_dismissed', false );
	// 	if ( $notice_name == true ) {
	// 		return;
    //     }
    //     $screen = get_current_screen();

    //     $allowed_screens = array(
    //         'toplevel_page_buddybot-chatbot',
    //         'buddybot_page_buddybot-playground',
    //         'buddybot_page_buddybot-files',
    //         'buddybot_page_buddybot-conversations',
    //         'buddybot_page_buddybot-assistants',
    //         'buddybot_page_buddybot-settings',
    //         'buddybot_page_buddybot-vectorstore',
    //     );

    //     if (!in_array($screen->id, $allowed_screens)) {
    //         return;
    //     }
        
	// 	$this->buddybotActivationModel();
    // }

    // public function bb_dismissible_notice_ajax()
    // {
    //     $nonce = filter_input( INPUT_POST, 'nonce' );
	// 	if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
	// 		die( esc_html__( 'Failed security check', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ) );
	// 	}

    //     if ( current_user_can( 'manage_options' ) ) 
    //     {
            
            
    //         if ( isset($_POST['notice_name'] ) ) {
    //                 $notice_name = sanitize_text_field($_POST['notice_name'] );
    //                 update_option( $notice_name, true );

    //         }
    //     }

    //     die;
    // }

}