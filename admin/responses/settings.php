<?php

namespace MetagaussOpenAI\Admin\Responses;

class Settings extends \MetagaussOpenAI\Admin\Responses\MoRoot
{
    public function getOptions()
    {
        $this->checkNonce('get_options');

        $section = sanitize_text_field($_POST['section']);
        $section_class = '\MetagaussOpenAI\Admin\Html\Views\Settings\\' . $section;
        $selection_object = new $section_class();
        $this->response['success'] = true;
        $this->response['html'] = $selection_object->getHtml();
        print_r($this->response);
        wp_die();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('wp_ajax_getOptions', array($this, 'getOptions'));
    }
}