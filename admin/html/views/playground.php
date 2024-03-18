<?php

namespace MetagaussOpenAI\Admin\Html\Views;

class Playground extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Playground', 'metagauss-openai');
        $this->pageHeading($heading);
        $this->playgroundContainer();
    }

    private function playgroundContainer()
    {
        echo '<div class="row border small">';
        
        $this->playgroundOptions();
        $this->threadsContainer();
        $this->messagesContainer();
        
        echo '</div>';
    }

    private function playgroundOptions()
    {
        echo '<div id="mgoa-playground-options-container" class="col-md-12 d-flex border-bottom">';
        
        echo '<div id="mgoa-playground-options-select-assistant" class="p-3">';
        echo '<label class="">';
        esc_html_e('Assistant', 'metagauss-openai');
        echo '<label>';
        echo '<select id="mgoa-playground-assistants-list" class="form-select ms-2">';
        echo '<option disabled>' . esc_html__('Loading...', 'metagauss-openai') . '</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div id="mgoa-playground-options-select-user" class="p-3">';
        echo '<label class="">';
        esc_html_e('User', 'metagauss-openai');
        echo '<label>';
        echo '<select id="" class="ms-2">';
        $this->getUsers();
        echo '</select>';
        echo '</div>';

        echo '</div>';
    }

    private function threadsContainer()
    {
        echo '<div id="mgoa-playground-threads-container" class="col-md-2 flex-column border-end bg-light">';
        
        echo '<div id="mgoa-playground-threads-header" class="fs-6 p-3">';
        esc_html_e('Conversations', 'metagauss-openai');
        echo '</div>';
        
        echo '<div id="mgoa-playground-threads-list" class="p-3">';
        $this->threadList();
        echo '</div>';
        
        echo '</div>';
    }

    private function messagesContainer()
    {
        echo '<div class="col-md-10 flex-column">';
        $this->messagesListContainer();
        $this->messagesStatusBar();
        $this->newMessageContainer();
        echo '</div>';
    }

    private function messagesListContainer()
    {
        echo '<div class="d-flex" style="min-height:500px;">';
        echo '</div>';
    }

    private function messagesStatusBar()
    {
        echo '<div class="">';

        echo '<div id="mgoa-playground-message-status" class="text-center">';
        echo '</div>';

        echo '<div class="progress" role="progressbar" style="height: 5px">';
        echo '<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>';
        echo '</div>';

        echo '</div>';
    }

    private function newMessageContainer()
    {
        echo '<div class="d-flex align-items-center mt-auto">';
        $this->attachFileBtn();
        $this->messageTextArea();
        $this->sendMessageBtn();
        echo '</div>';
    }

    private function attachFileBtn()
    {
        echo '<div class="p-2">';
        echo '<button type="button"';
        echo 'class="btn btn-light border btn-sm rounded-circle p-2">';
        $this->moIcon('attach_file');
        echo '</button>';
        echo '</div>';
    }

    private function messageTextArea()
    {
        echo '<div class="p-2 flex-fill">';
        echo '<textarea id="mo-playground-new-message-text" data-mo-threadid="" class="w-100" rows="5">';
        echo '</textarea>';
        echo '</div>';
    }

    private function sendMessageBtn()
    {
        echo '<div class="p-2">';
        echo '<button type="button"';
        echo 'class="btn btn-dark">';
        esc_html_e('Send', 'metagauss-openai');
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

            echo '<option' . $selected . '>' . $user->display_name . '</option>';
        }
    }

    private function threadList()
    {
        $response = $this->sql->getThreadsByUserId();

        if ($response['success'] === false) {
            esc_html_e('There was an error while fetching threads.', 'metagauss-openai');
            echo ' ';
            echo $response['message'];
            return;
        }

        if (empty($response['result'])) {
            echo '<span class="text-muted">';
            esc_html_e('No previous conversations.', 'metagauss-openai');
            echo '</span>';
            return;
        }

        foreach ($response['result'] as $thread) {
            echo '<div>';
            echo $thread->id;
            echo '</div>';
        }
    }
}