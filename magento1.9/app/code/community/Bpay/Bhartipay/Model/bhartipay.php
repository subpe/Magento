<?php
 
class Bpay_bhartipay_Model_bhartipay extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bhartipay/bhartipay');
    }
}