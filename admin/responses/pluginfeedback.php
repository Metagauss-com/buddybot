<?php

namespace BuddyBot\Admin\Responses;

class PluginFeedback extends \BuddyBot\Admin\Responses\MoRoot
{

    public function buddybotSendPluginFeedback()
    {
        $this->checkNonce('buddybot_plugin_deactivation');
        $feedback_message = isset($_POST['feedback_message']) ? sanitize_text_field($_POST['feedback_message']) : '';
        $temp_deactivate = isset($_POST['temp_deactivate']) ? filter_var($_POST['temp_deactivate'], FILTER_VALIDATE_BOOLEAN) : false;
        $from_email_address = '<' . get_option('admin_email') . '>';

        if (empty($feedback_message) && !$temp_deactivate) {
            $this->response['success'] = false;
            $this->response['message'] = esc_html__('Please provide feedback or check the temporary deactivation option.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }
        $email_message = '';

        if (!empty($feedback_message)) {
            $email_message .= "<br><u>User Feedback Message</u> - "; 
            $email_message .= $feedback_message . "<br>";
        }
            
        if ($temp_deactivate) {
            $email_message .= "<p>This is a temporary deactivation</p>";
        }

        $email_message .= "\n\r BuddyBot Plugin Version - " . BUDDYBOT_PLUGIN_VERSION;

        // Email headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From:" . $from_email_address . "\r\n";

        // Send feedback email
        if (wp_mail('buddybotfeedback@metagauss.com', 'BuddyBot Uninstallation Feedback', $email_message, $headers)) {
            error_log('Feedback email sent successfully.');
            $this->response['success'] = true;
            $this->response['message'] = esc_html__('Feedback sent successfully.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        } else {
            $this->response['success'] = false;
            
            global $phpmailer;
            if (isset($phpmailer) && $phpmailer->ErrorInfo) {
                $this->response['message'] =  $phpmailer->ErrorInfo;
            } else {
                $this->response['message'] = esc_html__('Failed to send feedback.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();

    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_buddybotSendPluginFeedback', array($this, 'buddybotSendPluginFeedback'));
    }
}