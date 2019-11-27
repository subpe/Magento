<?php


class Bpay_bhartipay_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bhartipay/info.phtml');
    }

    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }

    public function toPdf()
    {
        $this->setTemplate('bhartipay/pdf/info.phtml');
        return $this->toHtml();
    }
}