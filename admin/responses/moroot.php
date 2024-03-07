<?php

namespace MetagaussOpenAI\Admin\Responses;

class MoRoot extends \MetagaussOpenAI\Admin\MoRoot
{
    protected $response = array();
    protected $api_key = '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';

    protected function checkNonce($nonce)
    {
        $nonce_status = wp_verify_nonce($_POST['nonce'], $nonce);

        if ($nonce_status === false) {
            $this->response['success'] = false;
            $this->response['message'] = '<div>' . __('Nonce error.', 'metagauss-openai') . '</div>';
            echo json_encode($this->response);
            wp_die();
        }
    }
}