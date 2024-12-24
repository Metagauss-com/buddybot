<?php
namespace BuddyBot\Admin;

final class AdminMenu extends \BuddyBot\Admin\MoRoot
{
    protected $icon;

    public function topLevelMenu()
    {
        $this->mainMenuItem();
        $this->playgroundSubmenuItem();
        // $this->wizardSubmenuItem();
        // $this->orgFilesSubmenuItem();
        $this->assistantsSubmenuItem();
        // $this->addFileSubmenuItem();
        //$this->dataSyncSubmenuItem();
        $this->settingsSubmenuItem();
        $this->vectorStoreSubmenuItem();
    }

    public function hiddenMenu()
    {
        $this->editAssistantSubmenuItem();
        $this->defaultBuddyBotWizard();
    }

    public function mainMenuItem()
    {
        $icon = '<svg width="100%" height="100%" viewBox="0 0 512 424" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
    <g transform="matrix(1,0,0,1,0,-42.8978)">
        <path d="M441.803,214.844C472.163,227.124 495.829,251.877 506.735,282.758C508.21,286.883 509.333,291.238 510.19,295.535C510.695,298.07 510.73,302.703 511.768,304.808C511.84,304.958 511.923,305.1 512,305.248L512,319.918C510.905,321.48 510.575,328.085 510.1,330.398C508.55,337.923 506.395,345.625 503.123,352.59C488.558,383.587 464.005,404.915 431.902,416.51C431.527,423.885 432.513,460.188 431.26,463.647C430.837,464.808 429.883,465.943 428.755,466.467C427.328,467.133 425.858,466.855 424.513,466.135C421.56,464.553 417.22,460.44 414.308,458.248L377.745,430.587C374,427.788 367.26,422.068 363.29,420.143C362.703,419.858 362.085,419.723 361.438,419.665C355.995,419.173 350.217,419.575 344.745,419.583L312.53,419.6C305.58,419.598 298.555,419.813 291.623,419.272C281.645,418.495 271.428,416.465 262.13,412.703C236.848,402.467 214.005,381.883 203.242,356.575C213.789,355.865 224.632,356.363 235.204,356.402C248.244,356.45 261.42,356.84 274.44,356.063C301.929,354.558 328.729,346.884 352.848,333.61C388.904,313.807 417.077,282.193 432.612,244.102C434.475,239.486 436.253,234.791 437.678,230.018C439.178,224.997 440.33,219.875 441.803,214.844Z" style="fill:white;fill-rule:nonzero;"/>
    </g>
    <use xlink:href="#_Image1" x="0" y="0" width="413px" height="340px"/>
    <use xlink:href="#_Image2" x="98" y="48" width="215px" height="176px"/>
    <g transform="matrix(1,0,0,1,0,-42.8978)">
        <path d="M189.451,211.557C195.599,211.171 201.901,211.439 208.062,211.443C212.487,211.446 217.191,211.124 221.588,211.523C222.866,211.64 224.106,212.017 225.233,212.63C227.605,213.913 229.368,216.207 230.137,218.778C230.959,221.528 230.672,224.633 229.252,227.14C227.724,229.839 225.465,231.091 222.57,231.849C217.307,232.474 211.545,231.966 206.229,231.975C201.105,231.983 195.677,232.453 190.606,231.94C188.985,231.777 187.399,231.352 185.977,230.545C183.765,229.291 182.225,227.297 181.579,224.837C180.906,222.276 181.053,218.588 182.433,216.286C184,213.67 186.591,212.286 189.451,211.557Z" style="fill:white;fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,0,-42.8978)">
        <path d="M250.975,171.333C255.71,170.734 260.535,172.178 264.36,174.97C269.145,178.464 272.12,183.982 272.99,189.79C273.82,195.335 272.313,200.852 268.983,205.341C265.588,209.917 260.9,212.577 255.308,213.43C250.5,213.927 245.97,212.759 242.027,209.959C237.291,206.596 234.24,201.595 233.309,195.872C232.351,189.985 233.86,184.155 237.391,179.361C240.674,174.903 245.531,172.141 250.975,171.333Z" style="fill:white;fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,0,-42.8978)">
        <path d="M252.083,183.982C252.762,183.962 253.523,183.912 254.085,184.38C254.542,184.762 254.78,185.252 254.76,185.851C254.732,186.65 254.3,187.206 253.81,187.788C253.06,187.788 252.28,187.833 251.67,187.31C251.17,186.881 250.943,186.436 250.963,185.771C250.99,184.986 251.56,184.482 252.083,183.982Z" style="fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,0,-42.8978)">
        <path d="M155.906,171.305C160.705,170.969 165.22,171.514 169.358,174.143C174.399,177.345 177.744,182.773 178.959,188.565C180.124,194.114 178.756,199.96 175.649,204.66C172.45,209.499 168.036,212.315 162.407,213.469C157.211,213.928 152.464,213.172 148.079,210.208C143.242,206.938 139.998,201.52 138.907,195.827C137.839,190.248 139.448,184.446 142.628,179.808C145.746,175.26 150.515,172.303 155.906,171.305Z" style="fill:white;fill-rule:nonzero;"/>
    </g>
    <g transform="matrix(1,0,0,1,0,-42.8978)">
        <path d="M158.22,184.246C158.959,184.265 159.65,184.292 160.272,184.75C160.749,185.101 161.032,185.541 161.077,186.14C161.139,186.957 160.579,187.575 160.104,188.164C160.038,188.183 159.972,188.207 159.904,188.221C159.19,188.372 158.402,188.333 157.827,187.834C157.305,187.381 157.183,186.794 157.201,186.133C157.224,185.33 157.693,184.793 158.22,184.246Z" style="fill-rule:nonzero;"/>
    </g>
    <defs>
        <image id="_Image1" width="413px" height="340px" xlink:href="data:image/png;base64,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"/>
        <image id="_Image2" width="215px" height="176px" xlink:href="data:image/png;base64,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"/>
    </defs>
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
            __('Playground', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Playground', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-playground',
            array($this, 'playgroundMenuPage'),
            1
        );
    }

    public function wizardSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Wizard', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Wizard', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
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
            __('Files', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Files', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
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
            __('Add File', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Add File', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-addfile',
            array($this, 'addFileMenuPage'),
            1
        );
    }

    public function dataSyncSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Data Sync', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Data Sync', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-datasync',
            array($this, 'dataSyncMenuPage'),
            1
        );
    }

    public function assistantsSubmenuItem()
    {
        add_submenu_page(
            'buddybot-chatbot',
            __('Assistants', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Assistants', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-assistants',
            array($this, 'assistantsMenuPage'),
            1
        );
    }

    public function editAssistantSubmenuItem()
    {
        add_submenu_page(
            'buddybot-hidden-page',
            __('Edit Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Edit Assistant', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-editassistant',
            array($this, 'EditAssistantMenuPage'),
            1
        );
    }

    public function defaultBuddyBotWizard()
    {
        add_submenu_page(
            'buddybot-hidden-page',
            __('Default Buddybot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Default Buddybot', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
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
            __('Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Settings', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
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
            __('Vector Store', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            __('Vector Store', 'buddybot-ai-custom-ai-assistant-and-chat-agent'),
            'manage_options',
            'buddybot-vectorstore',
            array($this, 'vectorStoreMenuPage'),
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

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'topLevelMenu'));
        add_action( 'admin_menu', array($this, 'hiddenMenu'));
        add_action( 'wp_ajax_bb_dismissible_notice', array($this,'bb_dismissible_notice_ajax') );
		add_action( 'admin_notices',array($this,'bb_dismissible_notice') );
        add_action( 'admin_enqueue_scripts', array($this,'enqueue_scripts' ));
 
    }

    public function enqueue_scripts() 
    {
       
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'buddybotbanner', plugin_dir_url( __FILE__ ) . 'js/buddybotbanner.js', array( 'jquery' ),BUDDYBOT_PLUGIN_VERSION, true );
        wp_localize_script(
            'buddybotbanner',
            'bb_ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'ajax-nonce' ),
            )
        );
    }

    public function bb_dismissible_notice()
    {
        
		$notice_name = get_option( 'bb_dismissible_plugin', '0' );
		if ( $notice_name == '1' ) {
			return;
        }
		?>
        <div class="notice notice-info is-dismissible bb-dismissible" id="bb_dismissible_plugin">
        <p><?php esc_html_e( "BuddyBot lets you train ChatGPT Assistants using your WordPress pages and posts, all directly from your WordPress site BuddyBot isn't about admin efficiency or content generation—it's all about helping you connect with your users. It focuses on user engagement, simplifying interactions, and enhancing the customer journey by leveraging your content.", 'buddybot-ai-custom-ai-assistant-and-chat-agent' ); ?></p>
        </div>
        <?php
    }

    public function bb_dismissible_notice_ajax()
    {
        $nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ) );
		}

        if ( current_user_can( 'manage_options' ) ) 
        {
            
            
            if ( isset($_POST['notice_name'] ) ) {
                    $notice_name = sanitize_text_field($_POST['notice_name'] );
                    update_option( $notice_name, '1' );

            }
        }

        die;
    }

}