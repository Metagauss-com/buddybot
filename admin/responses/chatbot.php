<?php

namespace MetagaussOpenAI\Admin\Responses;

class ChatBot extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function addMessage()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'add_user_message');
        if ($nonce_status === false) {
            wp_die();
        }

        $thread_id = $_POST['thread_id'];
        $message = $_POST['message'];

        if ($thread_id === '') {
            echo 'empty thread id';
            wp_die();
        }

        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';
        $data = array(
            'role' => 'user',
            'content' => $message 
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_exec($ch);
        curl_close($ch);

        wp_die();
    }

    public function createThread()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'create_thread');
        if ($nonce_status === false) {
            wp_die();
        }

        $data = array();

        $ch = curl_init("https://api.openai.com/v1/threads");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_exec($ch);
        curl_close($ch);

        wp_die();
    }

    public function runAssistant()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'run_assistant');

        if ($nonce_status === false) {
            echo 'nonce error';
            wp_die();
        }

        $thread_id = $_POST['thread_id'];

        if ($thread_id === '') {
            echo 'empty thread id';
            wp_die();
        }

        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs';
        $data = array(
            'assistant_id' => 'asst_4W9MUaUMA53dxsJZcivuYGHw'
        );

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_exec($ch);
        curl_close($ch);
        wp_die();
    }

    public function runStatus()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'run_status');

        if ($nonce_status === false) {
            echo 'nonce error';
            wp_die();
        }

        $thread_id = $_POST['thread_id'];
        $run_id = $_POST['run_id'];

        if ($run_id === '') {
            echo 'empty run id';
            wp_die();
        }

        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/runs/' . $run_id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
        ));

        curl_exec($ch);
        curl_close($ch);
        wp_die();
    }

    public function fetchResponse()
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], 'fetch_response');

        if ($nonce_status === false) {
            echo 'nonce error';
            wp_die();
        }

        $thread_id = $_POST['thread_id'];

        if ($thread_id === '') {
            echo 'empty run id';
            wp_die();
        }

        $url = 'https://api.openai.com/v1/threads/' . $thread_id . '/messages';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'OpenAI-Beta: assistants=v1'
        ));

        curl_exec($ch);
        curl_close($ch);
        wp_die();
    }

    public function __construct()
    {
        add_action('wp_ajax_addMessage', array($this, 'addMessage'));
        add_action('wp_ajax_createThread', array($this, 'createThread'));
        add_action('wp_ajax_runAssistant', array($this, 'runAssistant'));
        add_action('wp_ajax_runStatus', array($this, 'runStatus'));
        add_action('wp_ajax_fetchResponse', array($this, 'fetchResponse'));
    }
}