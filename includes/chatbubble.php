<?php
namespace BuddyBot\Includes;

final class ChatBubble extends \BuddyBot\Admin\MoRoot
{
    private $response;

    public function getHtml($response)
    {
        $this->response = $response;
        // $this->test();
        echo '<div class="buddybot-row-container buddybot-mt-4 buddybot-p-2 buddybot-bg-lightBlue " style="min-height:60vh;border-radius:20px;" >';
        echo  '<div class="buddybot-row">';
        echo '<div class="buddybot-box-col-3 buddybot-bg-white buddybot-p-3 " style="border-radius:20px;">';
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

            echo '<div class="buddybot-threads-container buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-me-3 ">';
            echo '<div class="buddybot-threads-list-item buddybot-d-flex buddybot-align-items-center  buddybot-text-truncate buddybot-me-3" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            echo '<span class="buddybot-threads-icon buddybot-me-2"><svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#1f1f1f"><path d="M323.79-516q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm156 0q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5Zm156 0q15.21 0 25.71-10.29t10.5-25.5q0-15.21-10.29-25.71t-25.5-10.5q-15.21 0-25.71 10.29t-10.5 25.5q0 15.21 10.29 25.71t25.5 10.5ZM96-96v-696q0-29.7 21.15-50.85Q138.3-864 168-864h624q29.7 0 50.85 21.15Q864-821.7 864-792v480q0 29.7-21.15 50.85Q821.7-240 792-240H240L96-96Zm114-216h582v-480H168v522l42-42Zm-42 0v-480 480Z"/></svg></span>';
             echo '<div class="buddybot-threads-list-item-text buddybot-text-truncate buddybot-fs-6">';
             echo esc_html($label);
             echo '</div>'; 
                // esc_html_e($date);    
            echo '</div>';
            echo '<div class="buddybot-thread-delete buddybot-ms-1 buddybot-me-1 buddybot-d-none" style="cursor:pointer" data-modal="buddybot-del-conversation-modal">   
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#dc3545"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
            </div>';
            echo '</div>';
           
           
            
        }
    }

   

private function chatBuubbleLeft()
{
    echo '<div class="chat-bubble-left buddybot-cursor-pointer bodduybot-mb-0" >';
    echo '<div class="buddybot-d-flex buddybot-align-items-center buddybot-justify-content-between buddybot-mt-1 ">';
    echo '<h4>CHATs</h4>';
    echo '<button class=" buddybot-btn-new-thread buddybot-bg-primary buddybot-border-radius-5 buddybot-fw-medium" id="buddybot-new-thread-btn">New Chat+</button>';

    echo '</div>';
    echo '<div class="buddybot-position-relative buddybot-pt-3">';
     echo '<input type="text" id="buddybot-thread-list-search-input" class="buddybot-form-control  buddybot-input margin-mx-auto buddybot-box-w-100 buddybot-border" placeholder="Search..." />';
    echo '</div>';
    echo '<div id="buddybot-threads-list" class="buddybot-mt-1 buddybot-overflow-y " >';
    $this->threadList();
    echo '</div>';

    echo '<div class="buddybot-d-none-flex buddybot-justify-content-center buddybot-my-2" style="display:none" id="buddybot-thread-spinner">';
    echo '<span aria-hidden="true" class="spinner is-active"></span>';
    echo '</div>';
    echo '</div>';
}

    private function chatBuubbleRight()
    {
       echo '<div class="buddybot-d-flex buddybot-flex-column buddybot-align-items-center buddybot-h-100 buddybot-position-relative" style="height: 90vh;">';

       // Chat container (scrollable area)
       echo '<div id="buddybot-chat-container" class="buddybot-flex-grow-1 buddybot-overflow-auto buddybot-box-w-100 buddybot-p-3" style="max-width: 750px;">';
       // Dynamic messages will go here
       echo '</div>';

       // Input area - positioned at bottom
       echo '<div class="buddybot-new-message-input-wrapper buddybot-position-sticky buddybot-bottom-0 buddybot-d-none-flex buddybot-align-items-center buddybot-mt-2 buddybot-px-3">';
       echo '<textarea id="buddybot-new-message-input" class="buddybot-box-w-100 buddybot-overflow-y buddybot-bg-white  buddybot-fs-6 buddybot-p-3" placeholder="Type your message..." rows="3" "></textarea>';
       echo '<button id="buddybot-send-message-btn" class="buddybot-position-absolute buddybot-p-2 buddybot-cursor-pointer buddybot-bg-white  buddybot-text-dark buddybot-d-flex buddybot-align-items-center buddybot-justify-content-center buddybot-border-radius-50" aria-label="Send message">';
       echo '<svg width="22px" height="22px" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">';
       echo '<line x1="22" y1="2" x2="11" y2="13"></line>';
       echo '<polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>';
       echo '</svg>';
       echo '</button>';
       echo '</div>';

       echo '</div>';

    }

}

