<?php

class Mageplaza_BetterBlog_Block_Adminhtml_Betterblog_Renderer_Fieldset_Notification
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId      = $element->getHtmlId();
        $html        = '<tr id="row_' . $htmlId . '">'
            . '<td class="label" colspan="3">';

        $marginTop   = $fieldConfig->margin_top ? (string)$fieldConfig->margin_top : '0px';
        $customStyle = $fieldConfig->style ? (string)$fieldConfig->style : '';

        $html .= '<ul style="margin-top: ' . $marginTop
            . '" class="messages'
            . $customStyle . '">';
        $html .= '<li class="notice-msg">' . $element->getLabel() . '</li>';
        $html .= '</ul></td></tr>';

        return $html;
    }
}
