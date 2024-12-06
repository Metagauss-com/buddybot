<?php

namespace BuddyBot\Admin;

final class InitialChecks extends \BuddyBot\Admin\MoRoot
{

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

        if (empty($key)) {
            $this->errors += 1;
            $this->addAlert(
                // Translators: %s is url to BuddyBot settings page in admin area. This should not be changed.
                sprintf(wp_kses_post('<strong>BuddyBot Notice:</strong> OpenAI API Key is missing. Please configure your API Key to enable BuddyBot\'s features. <a href="%s">Go to Settings</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_url(admin_url('admin.php?page=buddybot-settings')))
            );
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
                    sprintf(wp_kses_post('<strong>BuddyBot Notice:</strong> No vector store detected. A vector store is required for BuddyBot to function properly. Please create one by clicking <a href="%s">here</a>.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'), esc_url(admin_url('admin.php?page=buddybot-vectorstore')))
                );
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
            $this->openaiApikeyCheck();
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
