<?php
 
class Bpay_subpe_Model_Mysql4_subpe extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('subpe/subpe', 'subpe_id');
    }
