<?php

namespace Bhartipaypg\Bhartipay\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $session;
	
    public function __construct(Context $context, \Magento\Checkout\Model\Session $session) {
        $this->session = $session;
        parent::__construct($context);
    }

    public function cancelCurrentOrder($comment) {
        $order = $this->session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    public function restoreQuote() {
        return $this->session->restoreQuote();
    }

    public function getUrl($route, $params = []) {
        return $this->_getUrl($route, $params);
    }

    public function checkString_e($value) {
	$myvalue = ltrim($value);
	$myvalue = rtrim($myvalue);
	if ($myvalue == 'null')
		$myvalue = '';
	return $myvalue;
    }

    public function getHashFromArray($arrayList, $key ,$payid,$cust_name) {
	$postdata=ksort($arrayList);
	$str = $this->getArray2Str($arrayList);
	$data_value=explode("|",$str);
    $post_group_data=array();
 	$post_group_data['PAY_ID'] = "$payid";
 	$post_group_data['ORDER_ID'] = $data_value[6];
 	$post_group_data['RETURN_URL'] = $data_value[0];
    $post_group_data['CUST_EMAIL'] = $data_value[2];
    $post_group_data['CUST_NAME'] = "$cust_name";
    $post_group_data['CUST_STREET_ADDRESS1'] = '';
    $post_group_data['CUST_CITY'] = '';
    $post_group_data['CUST_STATE'] = '';
    $post_group_data['CUST_COUNTRY']= '';
    $post_group_data['CUST_ZIP']='';
    $post_group_data['CUST_PHONE']='';
    $post_group_data['CURRENCY_CODE']= 356;
    $post_group_data['AMOUNT']=$data_value[7]*100;
    $post_group_data['PRODUCT_DESC']='';
    $post_group_data['CUST_SHIP_STREET_ADDRESS1']='';
    $post_group_data['CUST_SHIP_CITY']='';
    $post_group_data['CUST_SHIP_STATE']=''; 
    $post_group_data['CUST_SHIP_COUNTRY']='';
    $post_group_data['CUST_SHIP_ZIP']='';
    $post_group_data['CUST_SHIP_PHONE']='';
    $post_group_data['CUST_SHIP_NAME']='';
    $post_group_data['TXNTYPE']='SALE';

   ksort($post_group_data);
 
	$all = '';
        foreach ($post_group_data as $name => $value) {
            $all .= $name."=".$value."~";
        }

        $all = substr($all, 0, -1);
        $all.=$key;
         return strtoupper(hash('sha256', $all));
    }


 public function getArray2Str($arrayList) {
	$findme   = 'REFUND';
	$findmepipe = '|';
	$paramStr = "";
	$flag = 1;	
	foreach ($arrayList as $key => $value) {
		$pos = strpos($value, $findme);
		$pospipe = strpos($value, $findmepipe);
		if ($pos !== false || $pospipe !== false) 
		{
			continue;
		}
		if ($flag) {
                	$paramStr .= $this->checkString_e($value);
                	$flag = 0;
		} else {
			$paramStr .= "|" . $this->checkString_e($value);
		}
	}
	return $paramStr;
    }
    
}
