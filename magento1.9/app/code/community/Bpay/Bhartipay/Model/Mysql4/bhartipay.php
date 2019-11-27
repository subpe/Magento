<?php
 
class Bpay_bhartipay_Model_Mysql4_bhartipay extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('bhartipay/bhartipay', 'bhartipay_id');
    }
