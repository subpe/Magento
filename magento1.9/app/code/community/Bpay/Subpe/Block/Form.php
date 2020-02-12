<?php


class Bpay_subpe_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('subpe/form.phtml');
    }

    protected function _getConfig()
    {
        return Mage::getSingleton('subpe/config');
    }
}