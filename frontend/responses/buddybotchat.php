<?php
namespace BuddyBot\Frontend\Responses;

class BuddybotChat extends \BuddyBot\Frontend\Responses\Moroot
{
    public function getConversationList()
    {
        $buddybot_chat = \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat::getInstance();
        $buddybot_chat->conversationList();
        wp_die();
    }

    public function getThreadInfo()
    {
        echo 'thread_info';
    }

    public function getMessages()
    {
        $this->checkNonce('get_messages');

        $thread_id = $_POST['thread_id'];
        $limit = $_POST['limit'];
        $order = $_POST['order'];
        $after = '';
        $before = '';

        if (!empty($_POST['after'])) {
            $after = '&after=' . $_POST['after'];
        }

        if (!empty($_POST['before'])) {
            $before = '&before=' . $_POST['before'];
        }
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages?limit=' . $limit . '&order=' . $order . $after . $before;

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        $this->messagesHtml($output->data);

        echo wp_json_encode($this->response);
        wp_die();
    }

    private function messagesHtml($messages)
    {
        $html = '';
        $messages = array_reverse($messages);
        foreach ($messages as $message) {
            $html .= $this->chatBubbleHtml($message);
        }

        $this->response['html'] = $html;
    }

    private function chatBubbleHtml($message)
    {
        $chat_bubble = new \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\Messages();
        $chat_bubble->setMessage($message);
        return $chat_bubble->getHtml();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getConversationList', array($this, 'getConversationList'));
        add_action('wp_ajax_getThreadInfo', array($this, 'getThreadInfo'));
        add_action('wp_ajax_getMessages', array($this, 'getMessages'));
    }
}