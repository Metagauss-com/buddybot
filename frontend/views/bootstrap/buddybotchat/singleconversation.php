<?php
namespace BuddyBot\Frontend\Views\Bootstrap\BuddybotChat;

trait SingleConversation
{
    protected function singleConversationHtml($atts = array())
    {
        $html = '<div id="buddybot-single-conversation-wrapper" class="container-fluid">';
        $html .= $this->visitorAlertContainer();
        $html .= $this->conversationActions();
        $html .= $this->messagesBox();
        $html .= $this->statusBar();
        $html .= $this->newMessageInput($atts);
        $html .= $this->cookieConsentOffcanvas();
        $html .= '</div>';
        return $html;
    }

    private function visitorAlertContainer() {
        if (is_user_logged_in()) {
            return '';
        }
    
        $html = '<div class="Buddybot-alert"> <div class="buddybot-temp-chat-wrap">';
        $html .= esc_html__( 'Temporary Chat', 'buddybot-ai-custom-ai-assistant-and-chat-agent' );
        $html .= '<svg data-popover="true" data-target="#buddybot-popover-1" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M440-280h80v-240h-80v240Zm40-320q17 0 28.5-11.5T520-640q0-17-11.5-28.5T480-680q-17 0-28.5 11.5T440-640q0 17 11.5 28.5T480-600Zm0 520q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>';
        $html .= '<div id="buddybot-popover-1" class="buddybot-popover">' . esc_html__( 'You will not be able to access your chat history permanently because you are not logged in. If you log in, you can save and access your conversations later.', 'buddybot-ai-custom-ai-assistant-and-chat-agent' ) . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }    

    private function conversationActions()
    {
        $html = '<div class="d-flex justify-content-between align-items-center">';
        
        $html .= '<div class="d-flex">';

        $html .= '<button id="buddybot-single-conversation-back-btn" class="bg-transparent border-0 shadow-0 text-dark p-0" role="button">';
        $html .= $this->mIcon('arrow_back_ios');
        $html .= '</button>';

        $html .= '</div>';
        
        $html .= '<div class="d-flex align-items-center">';
        
        $html .= '<button id="buddybot-single-conversation-load-messages-btn" class="bg-transparent border-0 shadow-0 text-dark p-0 mx-1 mb-3" role="button">';
        $html .= $this->mIcon('refresh');
        $html .= '</button>';

        $html .= '<button id="buddybot-single-conversation-delete-thread-btn" class="bg-transparent border-0 shadow-0 text-dark p-0 mx-1 mb-3" role="button" ';
        $html .= 'data-bs-toggle="modal" data-bs-target="#buddybot-single-conversation-delete-modal">';
        $html .= $this->mIcon('delete');
        $html .= '</button>';
        
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }

    private function statusBar()
    {
        $html = '<div id="buddybot-single-conversation-status-bar">';
        $html .= '</div>';
        $html .= '<div id="buddybot-single-conversation-top-spinners" class="d-flex justify-content-center mb-2">';

        $html .= '<div class="spinner-grow spinner-grow-sm me-1" role="status"><span class="visually-hidden">Loading...</span></div>';
        $html .= '<div class="spinner-grow spinner-grow-sm me-1" role="status"><span class="visually-hidden">Loading...</span></div>';
        $html .= '<div class="spinner-grow spinner-grow-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        $html .= '</div>';
        return $html;
    }

    private function messagesBox()
    {
        $html = '<div id="buddybot-single-conversation-messages-wrapper" class="mb-4 small" style="max-height:400px;overflow:auto;">';
        $html .= '</div>';
        return $html;
    }

    private function newMessageInput($atts)
    {
        $html = '<div id="buddybot-single-conversation-new-messages-wrapper" class="">';
        ob_start();
        do_action('buddybot_playground_faqs1',$atts);
        $html .= ob_get_clean();

        $html .= '<div class="">';
        $html .= '<textarea id="buddybot-single-conversation-user-message" class="form-control rounded-4 p-3 border-bottom border-dark border-2 shadow-0" rows="3" ';
        $html .= 'placeholder="' . __('Type your question here.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '">';
        $html .= '</textarea>';
        $html .= '</div>';

        $html .= '<div class="text-center mt-3">';
        $html .= '<button id="buddybot-single-conversation-send-message-btn" type="button" class="btn btn-dark py-3 px-4 rounded-2">';
        $html .= __('Send', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= '</button>';
        $html .= '</div>';
        
        $html .= '</div>';
        return $html;
    }

    private function cookieConsentOffcanvas()
    {
        $html = '<div class="offcanvas offcanvas-bottom buddybot-offcanvas-wrap p-3" tabindex="-1" id="cookieConsentOffcanvas" aria-labelledby="cookieConsentOffcanvasLabel" data-bs-backdrop="static">';
        $html .= '<div class="offcanvas-header">';
        $html .= '<div class="offcanvas-title" id="cookieConsentOffcanvasLabel">';
        $html .= __('Cookies', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $html .= ' <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                        <path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-75 29-147t81-128.5q52-56.5 125-91T475-881q21 0 43 2t45 7q-9 45 6 85t45 66.5q30 26.5 71.5 36.5t85.5-5q-26 59 7.5 113t99.5 56q1 11 1.5 20.5t.5 20.5q0 82-31.5 154.5t-85.5 127q-54 54.5-127 86T480-80Zm-60-480q25 0 42.5-17.5T480-620q0-25-17.5-42.5T420-680q-25 0-42.5 17.5T360-620q0 25 17.5 42.5T420-560Zm-80 200q25 0 42.5-17.5T400-420q0-25-17.5-42.5T340-480q-25 0-42.5 17.5T280-420q0 25 17.5 42.5T340-360Zm260 40q17 0 28.5-11.5T640-360q0-17-11.5-28.5T600-400q-17 0-28.5 11.5T560-360q0 17 11.5 28.5T600-320ZM480-160q122 0 216.5-84T800-458q-50-22-78.5-60T683-603q-77-11-132-66t-68-132q-80-2-140.5 29t-101 79.5Q201-644 180.5-587T160-480q0 133 93.5 226.5T480-160Zm0-324Z"/>
                   </svg>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="buddybot-offcanvas-body">';
        $html .= '<p>' . __('We use cookies to manage your chat session during your visit. This helps us maintain your conversation for the duration of your session. No personal data is stored permanently, and the session data will be deleted after it expires. By continuing to use this site, you consent to our use of cookies.', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</p>';
        $html .= '</div>';
        $html .= '<div class="buddybot-offcanvas-footer text-end">';
        $html .= '<button type="button" class="btn btn-primary" id="buddybot-acceptCookies">' . __('I Accept', 'buddybot-ai-custom-ai-assistant-and-chat-agent') . '</button>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}