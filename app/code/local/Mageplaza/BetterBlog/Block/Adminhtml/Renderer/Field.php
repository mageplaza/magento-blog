<?php

class Mageplaza_BetterBlog_Block_Adminhtml_Renderer_Field extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $html = '<tr id="row_' . $htmlId . '">'
            . '<td class="label" ';

        $marginTop = $fieldConfig->margin_top ? (string)$fieldConfig->margin_top : '5px';
        $customStyle = $fieldConfig->style ? (string)$fieldConfig->style : '';

        $html .= '<div style="margin-top: ' . $marginTop
            . '; font-weight: bold;'
            . $customStyle . '">';
        $html .= $element->getLabel();
        $html .= '</div></td><td class="value" style="font-weight: bold;"><span id="' . $htmlId . '">'. $element->getComment().'</span></td></tr>';

        return $html;
    }
}