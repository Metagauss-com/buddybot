<?php

namespace MetagaussOpenAI\Admin\Html\Views;

final class OrgFiles extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Files', 'megaform-openai');
        $this->pageHeading($heading);
        $this->pageBtns();
        $this->filesTable();
    }

    public function pageBtns()
    {
        $addfile_page = get_admin_url() . 'admin.php?page=metagaussopenai-addfile';
        echo '<div class="mb-3">';
        echo '<a class="btn btn-dark btn-sm" role="button"';
        echo 'href="' . $addfile_page . '"';
        echo '>';
        echo esc_html(__('Add File', 'metagauss-openai'));
        echo '</a>';
        echo '</div>';
    }

    private function filesTable()
    {
        echo '<table class="table table-sm">';
        $this->tableHeader();
        $this->tableBody();
        echo '</table>';
    }

    private function tableHeader()
    {
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">' . esc_html(__('No.', 'metagauss-openai')) . '</th>';
        echo '<th scope="col"></th>';
        echo '<th scope="col">' . esc_html(__('File Name', 'metagauss-openai')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Purpose', 'metagauss-openai')) . '</th>';
        echo '<th scope="col">' . esc_html(__('Size', 'metagauss-openai')) . '</th>';
        echo '<th scope="col">' . esc_html(__('ID', 'metagauss-openai')) . '</th>';
        echo '</tr>';
        echo '</thead>';
    }

    private function tableBody()
    {
        echo '<tbody>';
        echo '<tr>';
        echo '<td colspan="6" class="p-5">';
        echo '<div class="spinner-border text-dark d-flex justify-content-center mx-auto" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>';
        echo '</td>';
        echo '</tbody>';
    }
    
}