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

    protected function fileSize($bytes)
    {

        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    protected function moIcon($icon)
    {
        $html = '<span class="material-symbols-outlined" style="font-size:20px;vertical-align:sub;">';
        $html .= esc_html($icon);
        $html .= '</span>';
        return $html;
    }

    protected function listBtns($item_type)
    {
        $info_btn_class = 'mo-listbtn-' . $item_type . '-info';
        $delete_btn_class = 'mo-listbtn-' . $item_type . '-delete';
        
        $html = '<div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic example">';
        $html .= '<button type="button" class="' . esc_attr($info_btn_class) . ' btn btn-outline-dark">' . $this->moIcon('info') . '</button>';
        $html .= '<button type="button" class="' . esc_attr($delete_btn_class) . ' btn btn-outline-dark">' . $this->moIcon('delete') . '</button>';
        $html .= '</div>';

        $html .= $this->listSpinner();
        
        return $html;
    }

    protected function listSpinner()
    {
        $html .= '<div class="mo-list-spinner spinner-border spinner-border-sm visually-hidden" role="status">';
        $html .= '<span class="visually-hidden">Loading...</span></div>';
        return $html;
    }
}