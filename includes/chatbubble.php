<?php
namespace BuddyBot\Includes;

final class ChatBubble extends \BuddyBot\Admin\MoRoot
{
    private $response;

    public function getHtml($response)
    {
        $this->response = $response;
        // $this->test();
        echo '<div class="buddybot-row-container buddybot-mt-4 buddybot-p-2 buddybot-bg-lightBlue buddybot-border-radius-20 buddybot-box-w-100"  >';
        echo  '<div class="buddybot-row">';
        echo '<div class="buddybot-box-col-3 buddybot-bg-white buddybot-p-3 buddybot-border-radius-20 buddybot-box-vh-95" id="buddybot-chat-left-container">';
        $this->chatBuubbleLeft();
        echo '</div>';
        echo '<div class="buddybot-box-col-9 buddybot-mx-auto">';
        $this->chatBuubbleRight();
        echo  '</div>';
        echo  '</div>';
        echo '</div>';
    }

    private function threadList()
    {
        
        if ($this->response['success'] === false) {
            esc_html_e('There was an error while fetching threads.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo ' ';
            echo esc_html($this->response['message']);
            return;
        }

        if (empty($this->response['result'])) {
            echo '<span class="text-muted">';
            esc_html_e('No previous conversations.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo '</span>';
            return;
        }

        foreach ($this->response['result'] as $thread) {
            
            $label = $thread->thread_name;

            if (empty($label)) {
                $label = $thread->thread_id;
            }
            // $date = get_date_from_gmt($thread->created, 'Y-m-d');

            echo '<div class="buddybot-threads-container buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-me-3 buddybot-p-1 buddybot-text-small buddybot-text-decoration-none">';
            echo '<div class="buddybot-threads-list-item buddybot-d-flex buddybot-align-items-center buddybot-text-truncate buddybot-me-3" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            echo '<span class="buddybot-threads-icon buddybot-me-2 buddybot-ms-1"><svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="#1f1f1f"><path d="M323.79-516q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm156 0q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm156 0q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5ZM96-96v-696q0-29.7 21.15-50.85Q138.3-864 168-864h624q29.7 0 50.85 21.15Q864-821.7 864-792v480q0 29.7-21.15 50.85Q821.7-240 792-240H240L96-96Zm114-216h582v-480H168v522l42-42Zm-42 0v-480 480Z"/></svg></span>';
             echo '<div class="buddybot-threads-list-item-text buddybot-text-truncate buddybot-text-small buddybot-mb-2">';
             echo esc_html($label);
             echo '</div>'; 
                // esc_html_e($date);    
            echo '</div>';
            echo '<div class="buddybot-thread-delete buddybot-ms-1 buddybot-me-2 buddybot-d-none"  data-modal="buddybot-del-conversation-modal">   
               <svg xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#f1312e"><path d="M312-144q-29.7 0-50.85-21.15Q240-186.3 240-216v-480h-48v-72h192v-48h192v48h192v72h-48v479.57Q720-186 698.85-165T648-144H312Zm336-552H312v480h336v-480ZM384-288h72v-336h-72v336Zm120 0h72v-336h-72v336ZM312-696v480-480Z"/></svg>
                </div>';
            echo '</div>';
           
           
            
        }
    }

   

private function chatBuubbleLeft()
{
    echo '<div id="buddybot-left-container" class="chat-bubble-left buddybot-cursor-pointer bodduybot-mb-0" >';
    echo '<div class="buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center  ">';
    echo '<p class="buddybot-text-dark buddybot-fs-5 buddybot-mb-0 buddybot-mt-1 buddybot-fw-medium">CHATs</p>';
    echo '<span id="buddybot-new-thread-btn" class="buddybot-mt-1" >';
    echo '<svg xmlns="http://www.w3.org/2000/svg" height="25px" viewBox="0 -960 960 960" width="25px" fill="#1f1f1f"><path d="M120-160v-600q0-33 23.5-56.5T200-840h480q33 0 56.5 23.5T760-760v203q-10-2-20-2.5t-20-.5q-10 0-20 .5t-20 2.5v-203H200v400h283q-2 10-2.5 20t-.5 20q0 10 .5 20t2.5 20H240L120-160Zm160-440h320v-80H280v80Zm0 160h200v-80H280v80Zm400 280v-120H560v-80h120v-120h80v120h120v80H760v120h-80ZM200-360v-400 400Z"/></svg>';
    echo '</span>';
    echo '</div>'; // End buddybot-d-flex
    echo '<div class="buddybot-position-relative buddybot-pt-3">';
     echo '<input type="text" id="buddybot-thread-list-search-input" class="buddybot-form-control  buddybot-input margin-mx-auto buddybot-box-w-100 buddybot-border" placeholder="Search..." />';
    echo '</div>';
    echo '<div id="buddybot-threads-list" class="buddybot-mt-1 buddybot-overflow-y" >';
    $this->threadList();
    echo '</div>';

    echo '<div class=" buddybot-mt-2  buddybot-position-absolute "  id="buddybot-thread-spinner" >';
    echo '<span aria-hidden="true" class="spinner is-active"></span>';
    echo '</div>';
    echo '</div>';
}

    private function chatBuubbleRight()
    {
      // Parent container for the entire chat interface
echo '<div id="buddybot-right-container"  class="buddybot-d-flex buddybot-flex-direction-column buddybot-align-items-center buddybot-position-relative buddybot-box-vh-95 buddybot-box-w-100">';
     echo '<div id="buddybot-previous-conversation-spinner" style="display: none;">';
    echo '<span aria-hidden="true" class="spinner is-active"></span>';
    echo '</div>';
    // Chat container (scrollable area) - THIS IS THE MAIN CONTENT AREA
    echo '<div id="buddybot-chat-container" class="buddybot-flex-grow-1 buddybot-overflow-y buddybot-p-3 buddybot-box-w-100" >';

    // Dynamic messages will go here
    echo '</div>';

    // Spinner/Error area 
    echo '<div class="buddybot-spinner-error">'; 
        echo '<div id="buddybot-conversation-spinner-container" class="buddybot-d-flex buddybot-justify-content-center" >';
        echo '<div class="buddybot-loading-spinner buddybot-border-radius-50" role="status" id="buddybot-conversation-spinner" style="display: none;">.</div>';
        echo '</div>';
        echo '<div class="buddybot-error-message " id="buddybot-conversation-error"></div>';
    echo '</div>';

    // Input area - THIS ALSO NEEDS TO STICK TO THE BOTTOM
    echo '<div class="buddybot-new-message-input-wrapper buddybot-d-flex buddybot-align-items-center buddybot-mt-2 buddybot-px-3 ">'; 
        echo '<textarea id="buddybot-new-message-input" class="buddybot-box-w-100 buddybot-flex-grow-1 buddybot-border-none buddybot-overflow-hidden buddybot-overflow-y buddybot-bg-white buddybot-text-small buddybot-p-3 " placeholder="Type your message..." rows="3"></textarea>';
        echo '<button id="buddybot-send-message-btn" class="buddybot-position-absolute buddybot-p-2 buddybot-cursor-pointer buddybot-bg-white buddybot-text-dark buddybot-d-flex buddybot-align-items-center buddybot-justify-content-center buddybot-border-radius-50" aria-label="Send message">';
        echo '<svg width="22px" height="22px" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">';
        echo '<line x1="22" y1="2" x2="11" y2="13"></line>';
        echo '<polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>';
        echo '</svg>';
        echo '</button>';
    echo '</div>'; // End buddybot-new-message-input-wrapper

echo '</div>'; // End parent-container
    }
}