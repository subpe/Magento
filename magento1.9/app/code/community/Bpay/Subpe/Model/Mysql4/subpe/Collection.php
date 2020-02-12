<?php
 
class Bpay_subpe_Model_Mysql4_subpe_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::__construct();
        $this->_init('subpe/subpe');
    }
}