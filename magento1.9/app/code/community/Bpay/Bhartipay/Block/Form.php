<?php


class Bpay_bhartipay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bhartipay/form.phtml');
    }

    protected function _getConfig()
    {
        return Mage::getSingleton('bhartipay/config');
    }
}