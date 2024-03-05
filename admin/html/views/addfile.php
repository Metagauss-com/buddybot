<?php

namespace MetagaussOpenAI\Admin\Html\Views;

final class AddFile extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        $heading = __('Add File', 'megaform-openai');
        $this->pageHeading($heading);
        $this->uploadArea();
    }

    private function uploadArea()
    {
        wp_enqueue_media();
        echo '<div class="p-4 border border bg-light rounded-3 w-50">';

        echo '<button class="btn btn-outline-dark btn-sm me-1" type="button" id="metagauss-openai-file-select-btn">';
        echo esc_html(__('Select File', 'metagauss-openai'));
        echo '</button>';

        echo '<button class="btn btn-dark btn-sm ms-1" type="button" id="metagauss-openai-file-upload-btn">';
        echo esc_html(__('Upload File', 'metagauss-openai'));
        echo '</button>';

        echo '</div>';
    }

    public function pageJs()
    {
        echo '
        <script>
        $(document).ready(function(){' . PHP_EOL;

        $this->openMediaWindow();
        
        echo 
        PHP_EOL . '});
        </script>';
    }

    private function openMediaWindow()
    {
        echo '
        $("#metagauss-openai-file-select-btn").click(function(e) {

            e.preventDefault();
            
            file_frame = wp.media({
                title: "Select a File to Upload",
                button: {
                    text: "Use This File",
                },
                multiple: false 
            });

            file_frame.open();

        });
        ';
    }

    public function mediaFiles()
    {
        wp_enqueue_media();
    }

    public function __construct()
    {
        $this->setAll();
        add_action('admin_enqueue_scripts', array($this, 'mediaFiles'));
    }
    
}