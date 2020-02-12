<?php
 
class Bpay_subpe_Model_subpe extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('subpe/subpe');
    }
}