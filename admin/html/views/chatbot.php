<?php

namespace MetagaussOpenAI\Admin\Html\Views;

class ChatBot extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
       echo '<div class="border p-3 rounded-3 bg-white mt-2 small">';

       echo '<div class="small">';
       echo '<span class="">' . esc_html(__('Thread ID:', 'metagauss-openai')) . '</span>';
       echo '<span class="code ms-2" id="thread-id-visible"></span>';
       echo '<input type="hidden" id="thread-id">';
       echo '</div>';

       echo '<div class="small mb-3">';
       echo '<span class="">' . esc_html(__('Run ID:', 'metagauss-openai')) . '</span>';
       echo '<span class="code ms-2" id="run-id-visible"></span>';
       echo '<input type="hidden" id="run-id">';
       echo '</div>';

       echo '<div class="mb-3">';
       echo '<div id="conversation" style="height:200px;"></div>';
       echo '</div>';

       echo '<div class="mb-3">';
       echo '<textarea id="user-message" class="form-control"></textarea>';
       echo '</div>';

       echo '<div class="text-end">';
       echo '<button id="mo-send-btn" type="button" class="btn btn-primary me-0">';
       esc_html_e( 'Send', 'metagaussopenai');
       echo '</button>';
       echo '</div>';

       echo '</div>';
    }
    
}