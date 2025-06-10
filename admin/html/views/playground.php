<?php

namespace BuddyBot\Admin\Html\Views;

class Playground extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Test Area', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->customPageHeading($heading);
        $this->playgroundContainer();
    }

    private function playgroundContainer()
    {
        echo '<div class=" buddybot-border buddybot-text-small buddybot-row-container buddybot-box-w-100">';

        $this->playgroundOptions();
        echo '<div class="buddybot-row">';
        $this->threadsContainer();
        $this->messagesContainer();
        echo '</div>'; 
        echo '</div>';
    }

    protected function customPageHeading($heading)
    {
        echo '<div class="buddybot-header-wrap">';

        echo '<hr class="wp-header-end">';
        
        echo '<div class="bb-top-head-section">';
        $this->documentationContainer('https://getbuddybot.com/test-area-submenu-in-buddybot/');
        echo '<h1 class="wp-heading-inline">';
        echo esc_html($heading);
        echo '</h1>';
        echo '</div>';
        echo '</div>';
    }

    private function playgroundOptions()
    {
        echo '<div id="buddybot-playground-options-container" class="buddybot-col-12 buddybot-d-flex buddybot-border-bottom">';
        
        echo '<div id="buddybot-playground-options-select-assistant" class="buddybot-p-3">';
        echo '<label class="">';
        esc_html_e('Select BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<label>';
        echo '<select id="buddybot-playground-assistants-list" class="form-select buddybot-ms-2">';

        $models = $this->sql->getModels('chatbot');
        if (!empty($models)) {
            foreach ($models as $model) {
                $display_text = esc_html($model['chatbot_name'] . ' (' . $model['assistant_model'] . ')');
                $value = esc_attr($model['assistant_id']);
                echo '<option value="' . $value . '">' . $display_text . '</option>';
            }
        } else {
            echo '<option disabled selected>' . esc_html__('No BuddyBot Found', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</option>';
        }

        echo '</select>';
        echo '</div>';

        echo '</div>';
    }

    private function threadsContainer()
    {
        
        echo '<div id="buddybot-playground-threads-container" class="buddybot-bg-light buddybot-box-col-2" >';
        
        echo '<div id="buddybot-playground-threads-header" class="buddybot-fs-6 buddybot-px-4 buddybot-py-2">';
        esc_html_e('History', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';

        $this->threatIdInput();
        $this->runIdInput();
        
        echo '<div id="buddybot-playground-threads-list" class="buddybot-px-3">';
        echo '<div id="buddybot-playground-threads-list-inner" style="overflow-y: auto; height: 100%;">';
        $this->threadList();
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        
    }

    private function messagesContainer()
    {
        echo '<div class="buddybot-box-col-10" >';
        $this->threadOperations();
        $this->messagesListContainer();
        $this->messagesStatusBar();
        $this->newMessageContainer();
        echo '</div>';
    }

    private function threadOperations()
    {
        echo '<div id="buddybot-playground-thread-operations" class="buddybot-d-flex buddybot-justify-content-between buddybot-p-3">';
        $this->loadMessagesBtn();
        $this->tokensDisplay();
        echo '<input id="buddybot-playground-first-message-id" type="hidden">';
        echo '<input id="buddybot-playground-last-message-id" type="hidden">';
        echo '<input id="buddybot-playground-has-more-messages" type="hidden">';
        $this->deleteThreadBtn();
        echo '</div>';
    }

    private function loadMessagesBtn()
    {
        echo '<button id="buddybot-playground-past-messages-btn" type="button" class="buddybot-btn-outline-black  bb-btn-sm" style="opacity:0;">';
        $this->moIcon('cached');
        // esc_html_e('Delete Thread', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
    }

    private function tokensDisplay()
    {
        echo '<span id="mgao-playground-tokens-display" class="buddybot-text-small  buddybot-text-muted">';
        echo '</span>';
    }

    private function deleteThreadBtn()
    {
        echo '<button id="buddybot-playground-delete-thread-btn" type="button" class="buddybot-btn-outline-danger bb-btn-sm" style="opacity: 0;">';
        $this->moIcon('delete');
        // esc_html_e('Delete Thread', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
    }

    private function messagesListContainer()
    {
        echo '<div id="buddybot-playground-messages-list" class="buddybot-p-3" style="overflow-y: auto;">';
        echo '</div>';
    }

    private function openAiBadge()
    {
        do_action('buddybot_playground_faqs');
        $badge_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-dark-badge.svg';
        echo '<div class="buddybot-text-align-center buddybot-my-2">';
        echo '<img width="150" src="' . esc_url($badge_url) . '">';
        echo '</div>';
    }

    private function messagesStatusBar()
    {
        echo '<div class="">';
        echo '<div id="buddybot-playground-message-status" class="buddybot-text-align-center buddybot-text-small ">';
        $this->statusBarMessage('start-conversation', __('Start new Conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent'));
        echo '</div>';
        $this->openAiBadge();
        echo '</div>';
    }

    private function statusBarMessage($attr, $text) {
        echo '<span data-buddybot-message="' . esc_attr($attr) . '">';
        echo esc_html($text);
        echo '</span>';
    }

    private function newMessageContainer()
    {
        echo '<div class="buddybot-d-flex buddybot-align-items-center buddybot-mt-auto buddybot-box-w-100">';
       // $this->attachFileBtn();
        $this->messageTextArea();
        $this->sendMessageBtn();
        echo '</div>';
    }

    private function attachFileBtn()
    {
        wp_enqueue_media();
        echo '<div class="buddybot-p-2 buddybot-box-w-100">';
        echo '<button id="mgao-playground-message-file-btn" type="button"';
        echo 'class="buddybot-btn-light  border bb-btn-sm  buddybot-p-2">';
        $this->moIcon('attach_file');
        echo '</button>';
        echo '</div>';
    }

    private function messageTextArea()
    {
        echo '<div class="buddybot-p-2  flex-fill buddybot-box-w-100">';
        
        echo '<div id="buddybot-playground-attachment-wrapper" class="rounded buddybot-p-2 buddybot-mb-2 buddybot-border buddybot-text-small buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-d-none">';

        echo '<div><img id="buddybot-playground-attachment-icon" src="" width="12" class="buddybot-me-2">';
        echo '<span id="buddybot-playground-attachment-name"></span></div>';

        echo '<div role="button" id="buddybot-playground-remove-attachment-btn">';
        $this->moIcon('close');
        echo '</div>';

        echo '<input id="buddybot-playground-attachment-url" type="hidden">';
        echo '<input id="buddybot-playground-attachment-mime" type="hidden">';

        echo '</div>';

        echo '<textarea id="mgao-playground-new-message-text" data-buddybot-threadid="" class="buddybot-box-w-100 buddybot-bg-light" rows="4" placeholder="' . esc_attr( esc_html__( 'Type your question or message here...', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ) ) . '">';
        echo '</textarea>';
        
        echo '</div>';
    }

    private function sendMessageBtn()
    {
        echo '<div class="buddybot-p-2">';
        echo '<button id="mgao-playground-send-message-btn" type="button"';
        echo 'class="buddybot-btn-black">';
        esc_html_e('Send', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
        echo '</div>';
    }

    private function getUsers()
    {
        $users = get_users(array('fields' => array('display_name', 'id')));
        $current_user_id = get_current_user_id();

        foreach ($users as $user) {
            $selected = '';

            if ($user->id == $current_user_id) {
                $selected = ' selected';
            }

            echo '<option value="' . esc_attr($user->id) . '"' . esc_attr($selected) . '>' . esc_html($user->display_name) . '</option>';
        }
    }

    private function threatIdInput()
    {
        $thread_id = '';

        if (!empty($_GET['thread_id'])) {
            $thread_id = sanitize_text_field($_GET['thread_id']);
        }

        echo '<input id="mgao-playground-thread-id-input" ';
        echo 'type="hidden" value="' . esc_attr($thread_id) . '">';
    }

    private function runIdInput()
    {
        echo '<input id="mgao-playground-run-id-input" ';
        echo 'type="hidden" value="">';
    }

    private function threadList()
    {
        $response = $this->sql->getThreadsByUserId();

        if ($response['success'] === false) {
            esc_html_e('There was an error while fetching threads.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo ' ';
            echo esc_html($response['message']);
            return;
        }

        if (empty($response['result'])) {
            echo '<span class="buddybot-text-muted">';
            esc_html_e('No previous conversations.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo '</span>';
            return;
        }

        foreach ($response['result'] as $thread) {
            
            $label = $thread->thread_name;

            if (empty($label)) {
                $label = $thread->thread_id;
            }

            echo '<div class="buddybot-playground-threads-list-item buddybot-mb-2 buddybot-p-2 buddybot-text-truncate buddybot-text-small" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            echo esc_html($label);
            echo '</div>';
        }
    }

    public function getInlineJs()
    {
        $js = 'jQuery(document).ready(function($){';
        $js .= $this->openMediaWindowJs();
        $js .= $this->selectAttachmentJs();
        $js .= '});';
        return $js;
    }

    private function openMediaWindowJs()
    {
        $title = esc_html__('Select a file to attach to your message', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $btn_label = esc_html__('Attach To Message', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        
        return '
        $("#mgao-playground-message-file-btn").click(function(e) {

            e.preventDefault();

            let file_frame;

            if (file_frame) {
                file_frame.open();
                return;
            }
            
            file_frame = wp.media({
                title: "' . esc_html($title) . '",
                button: {
                    text: "' . esc_html($btn_label) . '",
                },
                multiple: false 
            });

            file_frame.open();

            file_frame.on("select",function() {
                let attachment =  file_frame.state().get("selection").first();
                selectAttachment(attachment)
             });

        });
        ';
    }

    private function selectAttachmentJs()
    {   
        return '
        function selectAttachment(attachment) {
            if (
                typeof attachment === "object" &&
                !Array.isArray(attachment) &&
                attachment !== null
            ) {
                $("#buddybot-playground-attachment-wrapper").removeClass("visually-hidden");
                attachment = JSON.parse(JSON.stringify(attachment));
                $("#mgao-playground-message-file-btn").attr("data-buddybot-fileid", attachment.id);
                $("#buddybot-playground-attachment-icon").attr("src", attachment.icon);
                $("#buddybot-playground-attachment-name").text(attachment.filename);
                $("#buddybot-playground-attachment-url").val(attachment.url);
                $("#buddybot-playground-attachment-mime").val(attachment.mime);
            } else {
                deselectAttachment();
            }
        }

        $("#buddybot-playground-remove-attachment-btn").click(deselectAttachment);
        
        function deselectAttachment() {
            $("#buddybot-playground-attachment-wrapper").addClass("visually-hidden");
            $("#mgao-playground-message-file-btn").attr("data-buddybot-fileid", "");
            $("#buddybot-playground-attachment-icon").attr("src", "");
            $("#buddybot-playground-attachment-name").text("");
            $("#buddybot-playground-attachment-url").val("");
            $("#buddybot-playground-attachment-mime").val("");
        }
        ';
    }
}