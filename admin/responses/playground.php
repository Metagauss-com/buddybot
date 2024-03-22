<?php

namespace MetagaussOpenAI\Admin\Responses;

class Playground extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function getAssistantOptions()
    {
        $this->checkNonce('get_assistants');

        $url = 'https://api.openai.com/v1/assistants?limit=50';

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

        if ($output->object === 'list') {
            $this->response['success'] = true;
            $this->assistantOptionsHtml($output);
        } else {
            $this->response['success'] = false;
            $this->response['message'] = __('Unable to fetch assistants list.', 'metagauss-openai');
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    protected function assistantOptionsHtml($assistants)
    {
        $this->response['html'] = '';

        if (!is_array($assistants->data)) {
            return;
        }

        foreach ($assistants->data as $assistant) {
            $name = $assistant->name;
            $id = $assistant->id;

            if (empty($name)) {
                $name = $assistant->id;
            }

            $this->response['html'] .= '<option value="' . $id . '">' . $name . '</option>';
        }
    }

    public function createThread()
    {
        $this->checkNonce('create_thread');
        
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
                'wp_source' => 'wp_admin'
            )
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        if ($this->response['success']) {
            $insert = $this->sql->saveThreadId($output->id);
            if ($insert === false) {
                $this->response['success'] = false;
                $this->response['message'] = __('Unable to save thread in the database', 'metagauss-openai');
            }
        }

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createMessage()
    {
        $this->checkNonce('create_message');

        $thread_id = $_POST['thread_id'];
        $message = $_POST['message'];
        
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
            'content' => $message,
            'metadata' => array(
                'wp_user_id' => get_current_user_id(),
                'wp_source' => 'wp_admin'
            )
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        $this->sql->updateThreadName($thread_id, $message);

        $this->response['html'] = $this->chatBubbleHtml($output);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function createRun()
    {
        $this->checkNonce('create_run');

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
                'wp_source' => 'wp_admin'
            )
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($data));

        $output = $this->curlOutput($ch);
        $this->checkError($output);

        echo wp_json_encode($this->response);
        wp_die();
    }

    public function retrieveRun()
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

    public function listMessages()
    {
        $this->checkNonce('list_messages');

        $thread_id = $_POST['thread_id'];
        $limit = $_POST['limit'];
        $order = $_POST['order'];
        $after = '';

        if (!empty($_POST['before'])) {
            $after = '&after=' . $_POST['before'];
        }
        
        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages?limit=' . $limit . '&order=' . $order . $after;

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
        foreach ($messages as $message) {
            $html .= $this->chatBubbleHtml($message);
        }

        $this->response['html'] = $html;
    }

    private function chatBubbleHtml($message)
    {
        $chat_bubble = new \MetagaussOpenAI\Admin\Html\Elements\Playground\ChatBubble();
        $chat_bubble->setMessage($message);
        return $chat_bubble->getHtml();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getAssistantOptions', array($this, 'getAssistantOptions'));
        add_action('wp_ajax_createThread', array($this, 'createThread'));
        add_action('wp_ajax_createMessage', array($this, 'createMessage'));
        add_action('wp_ajax_createRun', array($this, 'createRun'));
        add_action('wp_ajax_retrieveRun', array($this, 'retrieveRun'));
        add_action('wp_ajax_listMessages', array($this, 'listMessages'));
    }
}