<?php

namespace MetagaussOpenAI\Admin\Sql;

use \MetagaussOpenAI\Traits\Singleton;

class MoRoot extends \MetagaussOpenAI\Admin\MoRoot
{
    use Singleton;
    protected $response = array('');

    protected function setResponse()
    {
        $this->response = array(
            'success' => false,
            'message' => '',
            'result' => ''
        );
    }

    protected function returnResponse()
    {
        global $wpdb;
        if ($wpdb->last_error) {
            $this->response['success'] = false;
            $this->response['message'] = $wpdb->last_error;
          } else {
            $this->response['success'] = true;
          }

        return $this->response;
    }
}