<?php

namespace MetagaussOpenAI\Admin\Html\Views;

class ChatBot extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
       echo '<div class="border p-3 rounded-3 bg-white mt-5">';

       echo '<div class="mb-3">';
       echo '<textarea rows="20" class="form-control"></textarea>';
       echo '</div>';

       echo '<div class="mb-3">';
       echo '<textarea class="form-control"></textarea>';
       echo '</div>';

       echo '<div class="text-end">';
       echo '<button type="button" class="btn btn-primary me-0">Primary</button>';
       echo '</div>';

       echo '</div>';
    }
    
}