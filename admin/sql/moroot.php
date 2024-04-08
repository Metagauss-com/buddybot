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

    public function getItemById(string $type, int $id, string $output = 'OBJECT')
    {
        $table = $this->config->getDbTable($type);

        if ($table === false) {
            return false;
        }

        global $wpdb;
        $item = $wpdb->get_row(
            $wpdb->prepare(
                'SELECT * FROM %i WHERE id=%d',
                $table, ($id)
            ), $output
        );

        return $item;
    }
}