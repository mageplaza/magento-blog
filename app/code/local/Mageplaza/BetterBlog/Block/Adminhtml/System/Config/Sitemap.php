<?php


class Mageplaza_BetterBlog_Block_Adminhtml_System_Config_Sitemap extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('adminhtml/betterblog_blog/generateSitemap');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('mageplaza_betterblog')->__('Generate Blog Sitemap'))
            ->setOnClick("return conformation();")
            ->toHtml()
        ;

        $html .= "<p class='note'>";
        $html .= "<span style='color:#E02525;'>";
        $html .= Mage::helper('mageplaza_betterblog')->__(
            "IT IS INTEGRATED WITH MAGENTO DEFAULT SITEMAP. <br>Generate sitemap and save to ".Mage::getBaseUrl(true) ."sitemap/blog.xml. "
        );
        $html .= "</span>";
        $html .= "</p>";
        $html .= "
            <script type='text/javascript'>
                function conformation (){
                    if (confirm('" . $this->__('Are you sure?') . "')) {
                        var url ='{$url}';
                        new Ajax.Request(url, {
                            parameters: {
                                         form_key: FORM_KEY,
                                         },
                            evalScripts: true,
                            onSuccess: function(transport) {
//                                if(transport.responseText =='success'){
                                 location.reload();
//                                }
                            }
                        });
                    }
                }
            </script>
        ";
        return $html;
    }
}