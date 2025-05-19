<?php
namespace BuddyBot\Includes;

final class ChatBubble extends \BuddyBot\Admin\MoRoot
{
    private $response;

    public function getHtml($response)
    {
        $this->response = $response;
        // $this->test();
        echo '<div class="buddybot-row-container buddybot-mt-4 buddybot-bg-pureLight " style="min-height:80vh" >';
        echo  '<div class="buddybot-row">';
        echo '<div class="buddybot-box-col-3 ">';
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

            echo '<div class="buddybot-playground-threads-list-item mb-2 p-2 text-truncate small" data-buddybot-threadid="' . esc_attr($thread->thread_id) . '" role="button">';
            echo esc_html($label);
            echo '</div>';
        }
    }

    private function chatBuubbleLeft()
    {
        echo '<div class="chat-bubble-left  buddybot-border-right" style="min-height:80vh!important;">';
        echo '<div class="buddybot-d-flex buddybot-align-items-center buddybot-justify-content-between  ">';
        echo '<h4>Chat</h4>';
        echo  '<span><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000"><path d="M120-160v-600q0-33 23.5-56.5T200-840h480q33 0 56.5 23.5T760-760v203q-10-2-20-2.5t-20-.5q-10 0-20 .5t-20 2.5v-203H200v400h283q-2 10-2.5 20t-.5 20q0 10 .5 20t2.5 20H240L120-160Zm160-440h320v-80H280v80Zm0 160h200v-80H280v80Zm400 280v-120H560v-80h120v-120h80v120h120v80H760v120h-80ZM200-360v-400 400Z"/></svg></span>';
        echo '</div>';
        echo '<div class="buddybot-search-box buddybot-mt-2">';
        echo '<input type="text" class="buddybot-form-control buddybot-input  " placeholder="Search..." />';
        echo '<button class="buddybot-btn-black "></span><svg  xmlns="http://www.w3.org/2000/svg" height="22px" viewBox="0 -960 960 960" width="22px" fill="#ffffff"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg></button>';
        echo '</div>';
        $this->threadList();
        echo '</div>';

        
    }
    private function chatBuubbleRight()
    {
        
    }

}