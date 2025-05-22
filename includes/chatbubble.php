<?php
namespace BuddyBot\Includes;

final class ChatBubble extends \BuddyBot\Admin\MoRoot
{
    private $response;

    public function getHtml($response)
    {
        $this->response = $response;
        // $this->test();
        echo '<div class="buddybot-row-container buddybot-mt-4 buddybot-p-3 buddybot-bg-lightBlue " style="min-height:60vh;border-radius:20px;" >';
        echo  '<div class="buddybot-row">';
        echo '<div class="buddybot-box-col-3 buddybot-bg-white buddybot-p-3 " style="border-radius:20px;">';
        $this->chatBuubbleLeft();
        echo '</div>';
        echo '<div class="buddybot-box-col-9">';
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

            echo '<div class="buddybot-threads-container buddybot-d-flex buddybot-justify-content-between buddybot-align-items-center buddybot-me-3 buddybot-mt-2">';
            echo '<div class="buddybot-threads-list-item buddybot-d-flex buddybot-align-items-center  buddybot-text-truncate buddybot-me-3" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            echo '<span class="buddybot-threads-icon buddybot-me-3"><svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px" fill="#212529"><path d="M240-400h320v-80H240v80Zm0-120h480v-80H240v80Zm0-120h480v-80H240v80ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z"/></svg></span>';
            echo '<div class="buddybot-threads-list-item-text buddybot-text-truncate ">';
            echo esc_html($label);
            echo '</div>'; 
                // esc_html_e($date);    
            echo '</div>';
            echo '<div class="buddybot-thread-delete buddybot-ms-1 buddybot-me-1 buddybot-d-none" style="cursor:pointer" data-modal="buddybot-del-conversation-modal">   
            <svg class="" xmlns="http://www.w3.org/2000/svg" height="21px" viewBox="0 -960 960 960" width="21px" fill="#dc3545"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg>
            </div>';
            echo '</div>';
           
           
            
        }
    }

   

private function chatBuubbleLeft()
{
    echo '<div class="chat-bubble-left buddybot-cursor-pointer " >';
    echo '<div class="buddybot-d-flex buddybot-align-items-center buddybot-justify-content-between  ">';
    echo '<h4>Chat</h4>';
    echo  '<span><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000"><path d="M120-160v-600q0-33 23.5-56.5T200-840h480q33 0 56.5 23.5T760-760v203q-10-2-20-2.5t-20-.5q-10 0-20 .5t-20 2.5v-203H200v400h283q-2 10-2.5 20t-.5 20q0 10 .5 20t2.5 20H240L120-160Zm160-440h320v-80H280v80Zm0 160h200v-80H280v80Zm400 280v-120H560v-80h120v-120h80v120h120v80H760v120h-80ZM200-360v-400 400Z"/></svg></span>';
    echo '</div>';
    echo '<div class="buddybot-thread-list-search-box buddybot-mt-2 buddybot-box-w-100 buddybot-mx-auto">';
    echo '<input type="text" id="buddybot-thread-list-search-input" class="buddybot-form-control buddybot-input margin-mx-auto buddybot-box-w-100 buddybot-border" placeholder="Search..." />';
    echo '</div>';

    echo '<div id="buddybot-threads-list" class="buddybot-mt-2 buddybot-overflow-y buddybot-py-3" style="max-height: 50vh;">';
    $this->threadList();
    echo '</div>';

    echo '<div class="buddybot-d-none-flex buddybot-justify-content-center buddybot-my-2" style="display:none" id="buddybot-thread-spinner">';
    echo '<span aria-hidden="true" class="spinner is-active"></span>';
    echo '</div>';
    echo '</div>';
}

    private function chatBuubbleRight()
    {
       echo '<div class="buddybot-d-flex buddybot-flex-column buddybot-align-items-center buddybot-overflow-y buddybot-h-100" style="height: 75vh;">';

// Chat container (scrollable area)
echo '<div id="buddybot-chat-container" class="buddybot-flex-grow-1 buddybot-overflow-auto buddybot-box-w-100 buddybot-p-3" style="max-width: 750px;">';
// Dynamic messages will go here
echo '</div>';

// Input area
echo '<div class="buddybot-chat-input-wrapper buddybot-position-relative buddybot-box-w-100 buddybot-d-none-flex buddybot-align-items-center buddybot-p-2   buddybot-my-auto buddybot-px-3" style="max-width:800px;">';
echo '<textarea id="buddybot-chat-input" class="buddybot-box-w-100 buddybot-overflow-y buddybot-bg-white buddybot-border buddybot-fs-6 buddybot-p-2" placeholder="Type your message..." rows="3" ></textarea>';
echo '<button id="buddybot-send-btn" class="buddybot-position-absolute buddybot-cursor-pointer buddybot-bg-white  buddybot-text-dark buddybot-d-flex buddybot-align-items-center buddybot-justify-content-center buddybot-border-radius-50" aria-label="Send message" disabled >';
echo '<svg width="22px" height="22px" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">';
echo '<line x1="22" y1="2" x2="11" y2="13"></line>';
echo '<polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>';
echo '</svg>';
echo '</button>';
echo '</div>';

echo '</div>';

    }

}

