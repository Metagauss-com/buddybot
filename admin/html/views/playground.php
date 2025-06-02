<?php

namespace BuddyBot\Admin\Html\Views;

class Playground extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Test Area', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->customPageHeading($heading);
        $this->threadOperations();
        $chatBubble = new \BuddyBot\Includes\ChatBubble();
        $chatBubble->getHtml($this->sql->getThreadsByUserId(0, 10, 0));
        
        $deleteConversation = new \BuddyBot\Admin\Html\CustomModals\DeleteConversation();
        $deleteConversation->getHtml();
    }
    
    private function playgroundContainer()
    {
        echo '<div class="row border small">';
        
        $this->playgroundOptions();
        $this->threadsContainer();
        $this->messagesContainer();
        
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
        $this->playgroundOptions();
        echo '</div>';
        echo '</div>';
    }

    private function playgroundOptions()
    {
        //echo '<div id="buddybot-playground-options-container" class="col-md-12 d-flex border-bottom">';
        
        //echo '<div id="buddybot-playground-options-select-assistant" class="p-3">';
        echo '<label class="">';
        esc_html_e('Select BuddyBot', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '<label>';
        echo '<select id="buddybot-playground-assistants-list" class="form-select ms-2">';

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
        // echo '</div>';

        // echo '</div>';
    }

    private function threadsContainer()
    {
        echo '<div id="buddybot-playground-threads-container" class="col-md-2 flex-column border-end bg-light">';
        
        echo '<div id="buddybot-playground-threads-header" class="fs-6 px-4 py-2">';
        esc_html_e('History', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</div>';

        $this->threatIdInput();
        $this->runIdInput();
        
        echo '<div id="buddybot-playground-threads-list" class="px-3">';
        echo '<div id="buddybot-playground-threads-list-inner" style="overflow-y: auto; height: 100%;">';
        $this->threadList();
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
    }

    private function messagesContainer()
    {
        echo '<div class="col-md-10 flex-column">';
        $this->threadOperations();
        $this->messagesListContainer();
        $this->messagesStatusBar();
        $this->newMessageContainer();
        echo '</div>';
    }

    private function threadOperations()
    {
        echo '<div>';
        echo '<input id="buddybot-playground-first-message-id" type="hidden">';
        echo '<input id="buddybot-playground-last-message-id" type="hidden">';
        echo '<input id="buddybot-playground-has-more-messages" type="hidden">';
         echo '<input id="mgao-playground-thread-id-input" type="hidden">';
        echo '</div>';
    }

    private function loadMessagesBtn()
    {
        echo '<button id="buddybot-playground-past-messages-btn" type="button" class="btn btn-outline-dark btn-sm" style="opacity:0;">';
        $this->moIcon('cached');
        // esc_html_e('Delete Thread', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
    }

    private function tokensDisplay()
    {
        echo '<span id="mgao-playground-tokens-display" class="small text-muted">';
        echo '</span>';
    }

    private function deleteThreadBtn()
    {
        echo '<button id="buddybot-playground-delete-thread-btn" type="button" class="btn btn-outline-danger btn-sm" style="opacity: 0;">';
        $this->moIcon('delete');
        // esc_html_e('Delete Thread', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</button>';
    }

    private function messagesListContainer()
    {
        echo '<div id="buddybot-playground-messages-list" class="p-3" style="overflow-y: auto;">';
        echo '</div>';
    }

    private function openAiBadge()
    {
        do_action('buddybot_playground_faqs');
        $badge_url = $this->config->getRootUrl() . 'admin/html/images/third-party/openai/openai-dark-badge.svg';
        echo '<div class="text-center my-2">';
        echo '<img width="150" src="' . esc_url($badge_url) . '">';
        echo '</div>';
    }

    private function messagesStatusBar()
    {
        echo '<div class="">';
        echo '<div id="buddybot-playground-message-status" class="text-center small">';
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
        echo '<div class="d-flex align-items-center mt-auto">';
       // $this->attachFileBtn();
        $this->messageTextArea();
        $this->sendMessageBtn();
        echo '</div>';
    }

    private function attachFileBtn()
    {
        wp_enqueue_media();
        echo '<div class="p-2">';
        echo '<button id="mgao-playground-message-file-btn" type="button"';
        echo 'class="btn btn-light border btn-sm rounded-circle p-2">';
        $this->moIcon('attach_file');
        echo '</button>';
        echo '</div>';
    }

    private function messageTextArea()
    {
        echo '<div class="p-2 flex-fill">';
        
        echo '<div id="buddybot-playground-attachment-wrapper" class="rounded p-2 mb-2 border small d-flex justify-content-between align-items-center visually-hidden">';

        echo '<div><img id="buddybot-playground-attachment-icon" src="" width="12" class="me-2">';
        echo '<span id="buddybot-playground-attachment-name"></span></div>';

        echo '<div role="button" id="buddybot-playground-remove-attachment-btn">';
        $this->moIcon('close');
        echo '</div>';

        echo '<input id="buddybot-playground-attachment-url" type="hidden">';
        echo '<input id="buddybot-playground-attachment-mime" type="hidden">';

        echo '</div>';

        echo '<textarea id="mgao-playground-new-message-text" data-buddybot-threadid="" class="w-100 form-control" rows="4" placeholder="' . esc_attr( esc_html__( 'Type your question or message here...', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ) ) . '">';
        echo '</textarea>';
        
        echo '</div>';
    }

    private function sendMessageBtn()
    {
        echo '<div class="p-2">';
        echo '<button id="mgao-playground-send-message-btn" type="button"';
        echo 'class="btn btn-dark">';
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
            echo '<span class="text-muted">';
            esc_html_e('No previous conversations.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo '</span>';
            return;
        }

        foreach ($response['result'] as $thread) {
            
            $label = $thread->thread_name;

            if (empty($label)) {
                $label = $thread->thread_id;
            }

            echo '<div class="buddybot-playground-threads-list-item mb-2 p-2 text-truncate small" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
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