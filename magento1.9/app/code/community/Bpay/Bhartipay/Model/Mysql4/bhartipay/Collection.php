<?php
 
class Bpay_bhartipay_Model_Mysql4_bhartipay_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::__construct();
        $this->_init('bhartipay/bhartipay');
    }
}