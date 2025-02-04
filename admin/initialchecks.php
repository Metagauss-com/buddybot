<?php

namespace BuddyBot\Admin;

final class InitialChecks extends \BuddyBot\Admin\MoRoot
{

    public $options;
    private $data;
    private $errors = 0;

    protected $capabilities;

    protected $html = '';

    protected function setCapabilities()
    {
        $this->capabilities = array(
            'settings' => 'manage_options'
        );
        $key = $this->options->getOption('openai_api_key', '');

        if (!empty($key)) {
            $this->capabilities['vectorstore'] = 'manage_options';
        }
    }

    protected function addAlert($error_text = '')
    {
        $this->html .= '<div class="notice notice-error">';
        $this->html .= '<p>' . $error_text . '</p>';
        $this->html .= '</div>';
    }

    private function capabilityCheck()
    {
        $capability = 'manage_options';

        $page = str_replace('buddybot-', '', sanitize_text_field($_GET['page']));

        if (array_key_exists($page, $this->capabilities)) {
            $capability = $this->capabilities[$page];
        }

        if (!current_user_can($capability)) {
            $this->errors += 1;
            $this->addAlert(
                __('You are not authorized to access this page.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
            );
        }
    }

    private function openaiApikeyCheck()
    {
        $key = $this->options->getOption('openai_api_key', '');
        $img_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/bb-progress-icon.svg';

        if (empty($key)) {
            $this->errors += 1;
           
            $this->html .= '<div class="banner card shadow-sm mb-4 bdb-banner-card">';
            $this->html .= '<div class="banner-container card-body d-flex align-items-center">';
            $this->html .= '<div class="banner-graphic me-3">';
            $this->html .= '<img width="72px" src="' . $img_url .'" alt="All works fine.">';
            $this->html .= '</div>';
            $this->html .= '<div class="banner-content">';
            $this->html .= '<h3 class="h3 text-dark mb-2">' . esc_html__('BuddyBot is Ready to Work for You!', 'buddybot-ai-custom-ai-assistant-and-chat-agent') .'</h3>';
            $this->html .=  '<p class="text-muted mb-0">' . sprintf(wp_kses_post(__('To unlock BuddyBot’s AI capabilities, add your OpenAI API key in the <a href="%s">Settings</a>. BuddyBot will then be ready to assist your users!', 'buddybot-ai-custom-ai-assistant-and-chat-agent')), esc_url(admin_url('admin.php?page=buddybot-settings'))) .' </p>';
            $this->html .=   '</div></div></div>';

            // $this->addAlert(
            //     // Translators: %s is url to BuddyBot settings page in admin area. This should not be changed.
            //     sprintf(wp_kses_post('<strong>BuddyBot Notice:</strong> OpenAI API Key is missing. Please configure your API Key to enable BuddyBot\'s features. <a href="%s">Go to Settings</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_url(admin_url('admin.php?page=buddybot-settings')))
            // );
       }
    }

    private function vectorStoreCheck()
    {
        $vectorstore = get_option('buddybot_vectorstore_data');
        $id = isset($vectorstore['id']) ? $vectorstore['id'] : '';
        $key = $this->options->getOption('openai_api_key', '');

        if (!empty($key)) {
            if (empty($id)) {
                $this->errors += 1;
                $this->addAlert(
                    // Translators: %s is url to Vector Store settings page in admin area. This should not be changed.
                    sprintf(wp_kses_post('<strong>BuddyBot Notice:</strong> No AI Training Knowledgebase found. BuddyBot requires an AI Training Knowledgebase to function. Please create one by clicking <a href="%s">here</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_url(admin_url('admin.php?page=buddybot-vectorstore')))
                );
            } else {
                $url = 'https://api.openai.com/v1/vector_stores/' . $id;

                $headers = array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $key,
                    'OpenAI-Beta' => 'assistants=v2'
                );

                $response = wp_remote_get($url, array(
                    'headers' => $headers,
                    'timeout' => 60,
                ));
                if (is_wp_error($response)) {
                    return;
                } else {
                    $output = json_decode(wp_remote_retrieve_body($response), true);
                    if (empty($output['id'])) {
                        $this->errors += 1;
                        $this->addAlert(
                            // Translators: %s is url to Vector Store settings page in admin area. This should not be changed.
                            sprintf(wp_kses_post('<strong>Unable to Access AI Training Knowledgebase:</strong> We couldn\'t access the AI Training Knowledgebase. This might happen if the AI Training Knowledgebase was deleted or the OpenAI API key was changed. Please verify the AI Training Knowledgebase exists and ensure the correct API key is configured in the <a href="%s">Settings</a>. Or to create a new AI Training Knowledgebase, please visit the <a href ="%s">AI Training Knowledgebase page.</a>', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_url(admin_url('admin.php?page=buddybot-settings')), esc_url(admin_url('admin.php?page=buddybot-vectorstore')))
                        );
                        delete_option('buddybot_vectorstore_data');
                    }
                }
            }
        }
    }

    public function hasErrors()
    {
        if ($this->errors > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function runChecks()
    {
        if (!empty($this->data['custom_checks']) and is_array($this->data['custom_checks'])) {
            foreach ($this->data['custom_checks'] as $custom_check) {
                $method = $custom_check . 'Check';
                if (method_exists($this, $method)) {
                    $this->$method();
                } else {
                    $this->errors += 1;
                    $this->addAlert(
                        __('Invalid custom check requested.', 'buddybot-ai-custom-ai-assistant-and-chat-agent')
                    );
                }
            }
        } else {
            $this->capabilityCheck();
            // $this->openaiApikeyCheck();
            $this->vectorStoreCheck();
        }
    }

    public function __construct($data = '')
    {
        $this->data = $data;
        $this->setAll();
        $this->runChecks();
        echo wp_kses_post($this->html);
    }
}

?>
<style>
.bdb-banner-card{
max-width: 100%;
}
</style>

<?php
