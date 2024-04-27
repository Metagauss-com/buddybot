<?php

namespace MetagaussOpenAI\Admin\Html\Views\Settings;

class MoRoot extends \MetagaussOpenAI\Admin\Html\Views\MoRoot
{
    public function getHtml()
    {
        return '';
    }

    protected function optionHtml(string $id = '', string $label = '', string $control = '', string $description = '')
    {
        $html = '<tr>';
		$html .= '<th>';
		$html .= '<label for="' . esc_attr($id) . '">';
        $html .= esc_html($label);
		$html .= '</label>';
		$html .= '</th>';
		$html .= '<td>';
		$html .= $control;
		$html .= '<p class="description">';
		$html .= esc_html($description);
        $html .= '</p>';
		$html .= '</td>';
	    $html .= '</tr>';
        
        return $html;
    }
}