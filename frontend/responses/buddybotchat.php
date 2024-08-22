<?php
namespace BuddyBot\Frontend\Responses;

class BuddybotChat extends \BuddyBot\Frontend\Responses\Moroot
{
    public function getConversationList()
    {
        $buddybot_chat = \BuddyBot\Frontend\Views\Bootstrap\BuddybotChat::getInstance();
        $buddybot_chat->conversationList($_POST['timezone']);
        wp_die();
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

    public function sendUserMessage()
    {
        $this->checkNonce('send_user_message');

        if (empty($_POST['thread_id'])) {
            $this->createThreadWithMessage();
        } else {
            $this->addMessageToThread();
        }
    }

    private function createThreadWithMessage()
    {
        $url = 'https://api.openai.com/v1/threads';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $data = array(
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'frontend'
            )
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));
        $output = $this->curlOutput($ch);
        $this->checkError($output);

        $save_thread = $this->sql->saveThreadInDb($this->response['result']->id);

        if ($save_thread === false) {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to save conversation in database.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            echo wp_json_encode($this->response);
            wp_die();
        }

        $this->addMessageToThread($this->response['result']->id);
        wp_die();
    }

    private function addMessageToThread($thread_id = false)
    {
        if ($thread_id === false) {
            $thread_id = sanitize_text_field($_POST['thread_id']);
        }

        $user_message = sanitize_textarea_field(wp_unslash($_POST['user_message']));
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $data = array(
            'role' => 'user',
            'content' => $user_message,
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'frontend'
            )
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));
        $output = $this->curlOutput($ch);
        $this->checkError($output);
        
        $this->sql->updateThreadName($thread_id, $user_message);

        $this->response['html'] = $this->chatBubbleHtml($output);
        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createFrontendRun()
    {
        $thread_id = $_POST['thread_id'];
        $assistant_id = $_POST['assistant_id'];
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';

        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'OpenAI-Beta: assistants=v1',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
            )
        );

        $data = array(
            'assistant_id' => $assistant_id,
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'frontend'
            )
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function retrieveFrontendRun()
    {
        $this->checkNonce('retrieve_run');

        $thread_id = $_POST['thread_id'];
        $run_id = $_POST['run_id'];
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;

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

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function deleteFrontendThread()
    {
        $this->checkNonce('delete_frontend_thread');

        $thread_id = $_POST['thread_id'];
        $user_id = get_current_user_id();

        if ($this->sql->isThreadOwner($thread_id, $user_id) === false) {
            $this->response['success'] = false;
            $this->response['message'] = __('You are not authorized to delete this thread.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
            return;
        }

        $url = 'https://api.openai.com/v1/threads/' . $thread_id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
            )
        );

        $output = $this->curlOutput($ch);
        $this->checkError($output);
        
        if ($this->response['result']->deleted) {
            $this->response['success'] = true;
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to delete conversation.', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        }

        if ($this->response['success']) {
            $this->sql->deleteThread($thread_id);
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getConversationList', array($this, 'getConversationList'));
        add_action('wp_ajax_getMessages', array($this, 'getMessages'));
        add_action('wp_ajax_sendUserMessage', array($this, 'sendUserMessage'));
        add_action('wp_ajax_createFrontendRun', array($this, 'createFrontendRun'));
        add_action('wp_ajax_retrieveFrontendRun', array($this, 'retrieveFrontendRun'));
        add_action('wp_ajax_deleteFrontendThread', array($this, 'deleteFrontendThread'));
    }
}