<?php

namespace MetagaussOpenAI\Admin\Html\Views;

final class ChatBot extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    protected $chatbot_id = 0;
    protected $context = 'create';
    protected $chatbot;
    protected $heading;

    protected function setChatbotId()
    {
        if (!empty($_GET['chatbot_id'])) {
            $this->chatbot_id = absint($_GET['chatbot_id']);
        }
    }

    protected function setContext()
    {
        $chatbot = $this->sql->getItemById('chatbot', $this->chatbot_id);

        if (is_object($chatbot)) {
            $this->context = 'edit';
            $this->chatbot = $chatbot;
        }
    }

    protected function setHeading()
    {
        if ($this->context === 'edit') {
            $this->heading = __('Edit Chatbot', 'metagauss-openai');
        } else {
            $this->heading = __('New Chatbot', 'metagauss-openai');
        }
    }

    protected function useSingleChatbot()
    {
        if ($this->chatbot_id != 1) {
            $location = admin_url() . 'admin.php?page=metagaussopenai-chatbot&chatbot_id=1';
            echo '
            <script>
            location.replace("' . $location . '");
            </script>
            ';
        }
    }

    public function getHtml()
    {
        $this->useSingleChatbot();
        $this->pageHeading($this->heading);
    }
    
}