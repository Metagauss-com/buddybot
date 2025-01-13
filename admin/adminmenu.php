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
        $icon = '<svg width="100%" height="100%" viewBox="0 0 512 512" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
    <path d="M441.803,214.844C472.163,227.124 495.829,251.877 506.735,282.758C508.21,286.883 509.333,291.238 510.19,295.535C510.695,298.07 510.73,302.703 511.768,304.808C511.84,304.958 511.923,305.1 512,305.248L512,319.918C510.905,321.48 510.575,328.085 510.1,330.398C508.55,337.923 506.395,345.625 503.123,352.59C488.558,383.587 464.005,404.915 431.902,416.51C431.527,423.885 432.513,460.188 431.26,463.647C430.837,464.808 429.883,465.943 428.755,466.467C427.328,467.133 425.858,466.855 424.513,466.135C421.56,464.553 417.22,460.44 414.308,458.248L377.745,430.587C374,427.788 367.26,422.068 363.29,420.143C362.703,419.858 362.085,419.723 361.438,419.665C355.995,419.173 350.217,419.575 344.745,419.583L312.53,419.6C305.58,419.598 298.555,419.813 291.623,419.272C281.645,418.495 271.428,416.465 262.13,412.703C236.848,402.467 214.005,381.883 203.242,356.575C213.789,355.865 224.632,356.363 235.204,356.402C248.244,356.45 261.42,356.84 274.44,356.063C301.929,354.558 328.729,346.884 352.848,333.61C388.904,313.807 417.077,282.193 432.612,244.102C434.475,239.486 436.253,234.791 437.678,230.018C439.178,224.997 440.33,219.875 441.803,214.844Z" style="fill:white;fill-rule:nonzero;"/>
    <path d="M0,172.284C0.059,172.157 0.121,172.031 0.177,171.903C1.271,169.415 1.858,161.366 2.464,158.126C3.548,152.623 4.984,147.195 6.764,141.876C19.773,102.72 50.398,71.57 88.135,55.496C102.523,49.44 117.694,45.443 133.198,43.625C141.795,42.618 150.599,42.947 159.241,42.957L196.863,42.978L243.946,43.026C255.085,43.016 266.508,42.538 277.592,43.735C291.89,45.279 306.277,48.557 319.623,53.951C357.628,69.312 385.878,96.176 401.928,133.999C412.683,159.351 414.77,189.257 408.23,215.959C406.523,222.925 404.073,229.573 401.383,236.213C390.773,262.393 369.828,285.678 346.145,300.785C338.81,305.465 331.105,309.285 323.1,312.663C292.01,325.783 269.65,324.06 237.079,324.337L206.263,324.473C201.825,324.488 197.323,324.725 192.894,324.61C185.554,329.545 178.655,335.445 171.66,340.878L135.451,368.663L125.033,376.712C123.272,378.07 121.5,379.648 119.581,380.763C118.454,381.418 117.509,381.73 116.2,381.43C114.927,381.14 113.332,380.155 112.641,379.02C111.47,377.09 112.135,327.648 112.068,321.055C75.459,312.385 40.321,287.05 20.529,255.04C13.47,243.621 7.721,230.339 4.29,217.36C3.296,213.6 2.539,209.758 1.834,205.934C1.389,203.518 1.051,196.7 0,194.962L0,172.284Z" style="fill:white;fill-rule:nonzero;"/>
    <use xlink:href="#_Image1" x="98" y="91" width="215px" height="176px"/>
    <use xlink:href="#_Image2" x="180" y="210" width="52px" height="24px"/>
    <use xlink:href="#_Image3" x="232" y="170" width="43px" height="45px"/>
    <path d="M252.083,183.982C252.762,183.962 253.523,183.912 254.085,184.38C254.542,184.762 254.78,185.252 254.76,185.851C254.732,186.65 254.3,187.206 253.81,187.788C253.06,187.788 252.28,187.833 251.67,187.31C251.17,186.881 250.943,186.436 250.963,185.771C250.99,184.986 251.56,184.482 252.083,183.982Z" style="fill:rgb(254,254,254);fill-rule:nonzero;"/>
    <use xlink:href="#_Image4" x="137" y="170" width="44px" height="45px"/>
    <path d="M158.22,184.246C158.959,184.265 159.65,184.292 160.272,184.75C160.749,185.101 161.032,185.541 161.077,186.14C161.139,186.957 160.579,187.575 160.104,188.164C160.038,188.183 159.972,188.207 159.904,188.221C159.19,188.372 158.402,188.333 157.827,187.834C157.305,187.381 157.183,186.794 157.201,186.133C157.224,185.33 157.693,184.793 158.22,184.246Z" style="fill:rgb(254,254,254);fill-rule:nonzero;"/>
    <defs>
        <image id="_Image1" width="215px" height="176px" xlink:href="data:image/png;base64,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"/>
        <image id="_Image2" width="52px" height="24px" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADQAAAAYCAYAAAC1Ft6mAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABtElEQVRYhd2Yv2pUQRSHv5NkY+WfQqMWaVQQmxDS+QyCBCKCgk8QsLDwUQRBLAyWgvgCNmKZXrCxsMgqKaJr3Ojms7j3cmdlk73LLszufnCYGZgz/H7cmWHOhTkjhk1QLwH3gPPAMtA6pQU4Bnr/tWkfYOmE6AHdAXEAvAd2I6JaozlqqLfVHbXr9PBVfa6ujWJmVf2QV/dQOurdJmY21HZmsU05Vp+ofccmEjMXgV1gtfnmnAq2I+JZNUgNvQYeZpE0Hm3gWkR0ABYA1KvA/ZyqxmAFeFwNFsp2i+LanFWeqmehNtT8GpxOLgA3oTZ0PZ+WiXEFakM/MgqZFH2GvmcUMikuQ23oW0Yhk6IN82NI4B3Uht4Af7PJGZ+PEbEHpaGI+AK8yippPN5WnfTpcwP4RP3VZoU9YC0i+s4QEfEZ2KYuwmYBgUeVmcEz9I76M1tR0JyOutnMtq47vUXeofpCvTVI+6n/FNR14AGwQfHeOwecGZY3aCmKrbw4Yt4v4LCMfWAHeBkR+ycljCoMiwpxicJYGi3gCPhTtkfJuBcRlrmtJJaT/iLwOzHRjQhH1Td3/AMYv69yY7/JTwAAAABJRU5ErkJggg=="/>
        <image id="_Image3" width="43px" height="45px" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACsAAAAtCAYAAAA3BJLdAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAClElEQVRYheXZuWsUURzA8e8vkigaD9CASEiIICKJhYWdRyFoY2OvnYKCnaKlpY36L5g0ItqIYKGoldpYeCEkHhHBIDHgVXgmfC12J7tZd7PjHjMJfiEw+8JuPrzNzr55A4uoaPYF1LXADmA3MAB0Fn8AxoBnwFPgUUT8avbvNQJco55Wn5q+KfWCujUrZK96Tv36D8hq3VWH2oUM9Yj6rUlkedPqeXVVK6Fr1CstRFb2XO1tBbRfHW8jNOmturkZ6Dp1LANo0pS6fT5T1VOX2g3cBeZ9chuaArZGxGS1X3bUeNII2UMBeoCLarrzv7o/w7e+Vser2aICugx4Dmxsdoqa7CcwFBGvygcr/w1OkT8UYClwonJwdmYtfKgmgeUZoubrB9AXEVPJQPnMHmDhQAGWAcfKB8pn9iawN2tRnT4AGyJiBopYdT0wQe1TWZ5ti4jHUMLtY2FCAXYlBwmw8e/l9vcXdlNOkDQtKmyP2gUlbH+OmDStgBL2W46QNHVDCfslR0iaVsLiwX6EEvZzjpB6faawZpnFPsnPUrfRiBBK2Ns5Yuo1mhwk2PsUFrwLsVvJQQdARHwHHuTGqd1X4FryoHzxMpw5pX5XixMJzF3PdlLY9RvIQ1WjnRFxL3kwO7MR8Rs4mwupejcofJZmq7y67QJeAn0Zoqr1AxiMiPHywTkL7uJm7yFgJkNYtc5WQmumnspvf8OHFvYv0qV2qNdzgL5Qe/75fVBXq3cyhL5XGz8TqV3qSAbQN7biXoOFLfozbYReVlc3Da1A71QftxD5RT1s2u3NBsBL1KPqRBPId+pJW3njIwV6jzpcnKF6TaiX1IMWr1QbqRV3GDuAXmAQ2EJhQ20a+A18Au4Br5MF9H/THzhrOxkNP7RIAAAAAElFTkSuQmCC"/>
        <image id="_Image4" width="44px" height="45px" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAtCAYAAADV2ImkAAAACXBIWXMAAA7EAAAOxAGVKw4bAAACnklEQVRYheXZS4hNcRzA8e8PeZQhjzySZx6lLMRCsbHRyJREoZSdlYUt2Sk7WdnYi6KUQh5rkaGJoqFsRmwMYjzLfC3uPe65d+6de9zHOSPfus05/9PM+dz/3HvuOefCP1a0+wfUmcB2oBdYCHwBRso/k+URYADoj4hf7e6zFeQkdb96V/1h9obVi2qf2vZkZYXuU5/+BbJRD9VdXYOrq8o76XS31Hmdxu5U33cBm/RK3dAp7Al1tIvYpBF1b7vYozlA042qva1ie9VfOYNVP6hrGrnqvkPV9cB9oKelZ9t+z4AtEfG5dsOk2gFLh5nzFIcFWA+cq7dhzAyr+4DL3RZlbGNEDKQHqsDqdEr/jpV5qsbpekT0pQdqXxJHmDhYgF3q1vRALfhwjpisHU+v/HlJqGuBwdw5zfsJzE+OGOkZPlCMp2lTgR3JShq8J39L5nYnCwGl00bgKzCtKFGThiNiPlRmeAkTFwswT+2BCnh1gZisLYYKeEVxjsxVgb8XCMnaIqiAhwuEZK3qOPyuQEjWhuDfAr+GCngIeFucpWlfgI9QBkeEwI0iRU26VzZWfTRPZPDVZCF9tjaL0tFiShGiJi2JiDeQmuGI+ARcKozUuAcJFsaewJ8CRvP1NO1seqUKHBEvgAu5csbvMTUXxPWumtcAz4HJOaHGa0dE3EkPjLkvEREvgZO5kRp3qxbbMEv3ga8VcJsqaUhdUM/W8GayOgfoB1a1ND+t9x3YFhGP/vo31Q3q25xn91BbT1ddpj7JAfpNPdgWNoXuUa93Efta3dwRbAo9RT2mvusw9oq6qKPYGvhs9bT6tU3oTXVT16B14HPVQ+UZGsmIHFTPqFta3W9HviNTZwDrKF19LweWljf9KD+GgdvlD6X/q9/nw9oiHhyNvgAAAABJRU5ErkJggg=="/>
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
       wp_enqueue_style('buddybotbanner', plugin_dir_url(__FILE__) . 'css/global.css', array(), BUDDYBOT_PLUGIN_VERSION);
        wp_enqueue_script( 'jquery' );
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

    public function buddybotActivationModel()
    {

        $buddybotModalLabel = 'buddybot-welcome-modal';
        $welcomeImage = plugin_dir_url(__FILE__) . 'html/images/bb-welcome-image.png';
        $buddybotModalHeading = esc_html__('Welcome to BuddyBot! ', 'buddybot-ai-custom-ai-assistant-and-chat-agent');

        echo ' <div class="modal fade" id=' . esc_html($buddybotModalLabel) . ' data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby=' . esc_html($buddybotModalLabel) . 'aria-hidden="true">';
        echo ' <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"> ';
        echo ' <div class="modal-content"> ';
        echo ' <div class="modal-body d-flex align-items-center p-4">';

        // Left Section: Image
        echo ' <div class="bb-modal-image">';
        echo ' <img src="' . esc_url($welcomeImage) . '" alt="BuddyBot Welcome" class="bb-image">';
        echo ' </div>';

        //Right Section: Text and Actions    
        echo ' <div class="bb-modal-text">';
        echo ' <h1 class="bb-modal-title">' . esc_html($buddybotModalHeading) . '</h1>';
        echo ' <p class="bb-modal-description">' . esc_html__("BuddyBot is built to provide direct, AI-driven support to your website visitors. It uses your WordPress content to interact with users on your site, making your website more helpful and interactive. Let's set up your first BuddyBot to enhance the frontend user experience!", "buddybot-ai-custom-ai-assistant-and-chat-agent") . '</p>';
        echo ' <div class="bb-modal-actions">';
        echo ' <button type="button" class="btn btn-outline-dark bb-dismiss-modal" data-bs-dismiss="modal">' .esc_html__('Close ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button> ';
        echo ' <button type="button" class="btn btn-dark bb-get-started">' .esc_html__('Get Started ', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button> ';
        echo ' </div>';
        echo ' </div>';
        echo ' </div>';
        echo ' </div>';
        echo ' </div>';
        echo ' </div>';
    }

    public function bb_dismissible_notice()
    {
        
		$notice_name = get_option( 'buddybot_welcome_modal_dismissed', false );
		if ( $notice_name == true ) {
			return;
        }
        $screen = get_current_screen();

        $allowed_screens = array(
            'toplevel_page_buddybot-chatbot',
            'buddybot_page_buddybot-playground',
            'buddybot_page_buddybot-files',
            'buddybot_page_buddybot-addfile',
            'buddybot_page_buddybot-assistants',
            'buddybot_page_buddybot-settings',
            'buddybot_page_buddybot-vectorstore',
        );

        if (!in_array($screen->id, $allowed_screens)) {
            return;
        }
        
		$this->buddybotActivationModel();
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
                    update_option( $notice_name, true );

            }
        }

        die;
    }

}