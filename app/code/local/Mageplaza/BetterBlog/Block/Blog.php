<?php

class Mageplaza_BetterBlog_Block_Blog extends Mage_Core_Block_Template
{

    public function addToplink()
    {
        $name = Mage::helper('mageplaza_betterblog/config')->getGeneralConfig('name');

        $this->getParentBlock()->addLink(
            $name,
            Mage::helper('mageplaza_betterblog/config')->getBlogRoute(),
            $name,
            true);
    }
}
