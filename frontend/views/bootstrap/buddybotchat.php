<?php
namespace BuddyBot\Frontend\Views\Bootstrap;

use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\ConversationList;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SecurityChecks;
use BuddyBot\Frontend\Views\Bootstrap\BuddybotChat\SingleConversation;

class BuddybotChat extends \BuddyBot\Frontend\Views\Bootstrap\MoRoot
{
    use SecurityChecks;
    use ConversationList;
    use SingleConversation;

    protected $sql;

    public function shortcodeHtml($atts, $content = null)
    {
        $html = $this->securityChecksHtml();

        if (!$this->errors) {
            $html .= $this->conversationListHtml();
            $html .= $this->singleConversationHtml();
        }

        return $html;
    }
}