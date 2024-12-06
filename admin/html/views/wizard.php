<?php

namespace BuddyBot\Admin\Html\Views;

final class Wizard extends \BuddyBot\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = esc_html__('Setup Wizard', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        $this->pageHeading($heading);
        $this->wizardBlocks();
    }

    private function wizardBlocks()
    {
        $url = get_admin_url(null, 'admin.php?page=buddybot-defaultwizard');
        echo '<a href="' . esc_url( $url) . '">';
        esc_html_e('BuddyBot Wizard', 'buddybot-ai-custom-ai-assistant-and-chat-agent');
        echo '</a>';
    }
    
}