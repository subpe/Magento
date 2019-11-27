<?php


class Bpay_bhartipay_Model_Cc extends Mage_Payment_Model_Method_Abstract

{	
	//unique internal payment method identifier
	
	
	protected $_code = 'bhartipay_cc';
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_paymentMethod			= 'cc';
    protected $_defaultLocale			= 'en';
    protected $_liveUrl	= NULL;
    protected $_formBlockType = 'bhartipay/form';
    protected $_infoBlockType = 'bhartipay/info';
    protected $_order;
	
		public function isAvailable($quote = null)
    {
			if($this->getConfigData('active')==1){
        return true;
			}
			return false;
    }
    
    //Get order model
    
	 
    public function getOrder()
    {
		if (!$this->_order) {
			$this->_order = $this->getInfoInstance()->getOrder();
		}
		return $this->_order;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('bhartipay/processing/redirect');
    }

   
    // Return payment method type string
     
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    public function getUrl()
    {
    		$transaction_url = Mage::getStoreConfig('payment/bhartipay_cc/transaction_url');
			$const = (string)Mage::getConfig()->getNode('global/crypt/key');
			$this->_liveUrl= Mage::helper('bhartipay')->decrypt_e($transaction_url,$const);
			return $this->_liveUrl;
    }

    


    
    //prepare params array to send it to gateway page via POST
    public function getFormFields()
    {
		
		$price      = number_format($this->getOrder()->getGrandTotal(),2,'.','');
        $currency   = $this->getOrder()->getOrderCurrencyCode();
 		$locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
		if (is_array($locale) && !empty($locale))
			$locale = $locale[0];
		else
			$locale = $this->getDefaultLocale();
		 
		
		$const = (string)Mage::getConfig()->getNode('global/crypt/key');// Mage::getStoreConfig('payment/bhartipay_cc/constbhartipay');
		$mer = Mage::helper('bhartipay')->decrypt_e($this->getConfigData('inst_key'),$const);
		$merid = Mage::helper('bhartipay')->decrypt_e($this->getConfigData('inst_id'),$const);
		$website = $this->getConfigData('website');
		$industry_type = $this->getConfigData('industrytype');
		$is_callback = $this->getConfigData('custom_callbackurl');
		$callbackUrl = rtrim(Mage::getUrl('bhartipay/processing/response',array('_nosid'=>true)),'/');
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
		$order = Mage::getSingleton('sales/order');

		$order_data=$order->load($lastOrderId);
		$customer_name=$order_data['customer_firstname']." ".$order_data['customer_lastname'];
		$order->load($lastOrderId);
		$_totalData = $order->getData();
		$email = $_totalData['customer_email'];
		$telephone = $order->getBillingAddress()->getTelephone();
		//create array using which checksum is calculated
		
			$params = 	array(
			'MID' =>	$merid,  				
			'TXN_AMOUNT' =>	$price,
			'CHANNEL_ID' => "WEB",
			'INDUSTRY_TYPE_ID' => $industry_type,
			'WEBSITE' => $website,
			'CUST_ID' => Mage::getSingleton('customer/session')->getCustomer()->getId(),
			'ORDER_ID'	=>	$this->getOrder()->getRealOrderId(),   				    
			'EMAIL'=> $email,
			'MOBILE_NO' => preg_replace('#[^0-9]{0,13}#is','',$telephone)
		);


		if($is_callback=='1'){
			$callbackUrl=$this->getConfigData('callback_url')!=''?$this->getConfigData('callback_url'):$callbackUrl;
		}
		$params['CALLBACK_URL'] = $callbackUrl;
					
		//generate customer id in case this is a guest checkout
		if(empty($params['CUST_ID'])){
			$params['CUST_ID'] = $email;
		}
				
		if(Mage::getSingleton('core/session')->getPROMO_CAMP_ID()){
			$params['PROMO_CAMP_ID'] = Mage::getSingleton('core/session')->getPROMO_CAMP_ID();

			// unset Promo Code form session
			Mage::getSingleton('core/session')->unsPROMO_CAMP_ID();
		}

       //for Session
            $const = (string)Mage::getConfig()->getNode('global/crypt/key');// Mage::getStoreConfig('payment/bhartipay_cc/constbhartipay');
		$mer = Mage::helper('bhartipay')->decrypt_e($this->getConfigData('inst_key'),$const);
		$merid = Mage::helper('bhartipay')->decrypt_e($this->getConfigData('inst_id'),$const);
		$website = $this->getConfigData('website');
		$industry_type = $this->getConfigData('industrytype');
		$is_callback = $this->getConfigData('custom_callbackurl');
		$callbackUrl = rtrim(Mage::getUrl('bhartipay/processing/response',array('_nosid'=>true)),'/');
		$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
	
		$order = Mage::getSingleton('sales/order');

		$order_data=$order->load($lastOrderId);

		$customer_name=$order_data['customer_firstname']." ".$order_data['customer_lastname'];
		$order->load($lastOrderId);
		$_totalData = $order->getData();
		$email = $_totalData['customer_email'];
		$telephone = $order->getBillingAddress()->getTelephone();

		//for session
            $price=round($price);
			$checksum = Mage::helper('bhartipay')->getChecksumFromArray($params, $mer,$customer_name);//generate checksum
			$params['HASH'] = $checksum;
			$params_data = array(
            "PAY_ID" => $merid,
            "ORDER_ID" => $this->getOrder()->getRealOrderId(),
            "RETURN_URL" => $callbackUrl,
            "CUST_EMAIL" => $email,
            "CUST_NAME" => $customer_name,
            "CUST_STREET_ADDRESS1" => "",
            "CUST_CITY" => "",
            "CUST_STATE" =>"",
            "CUST_COUNTRY" => "",
            "CUST_ZIP" => "",
            "CUST_PHONE" => "Nan",
            "CURRENCY_CODE" => 356,
            "AMOUNT" => $price*100,
            "PRODUCT_DESC" => "Payment",
            "CUST_SHIP_STREET_ADDRESS1" => "",
            "CUST_SHIP_CITY" =>"",
            "CUST_SHIP_STATE" =>"",
            "CUST_SHIP_COUNTRY" =>"",
            "CUST_SHIP_ZIP" => "",
            "CUST_SHIP_PHONE" => "",
            "CUST_SHIP_NAME" => "",
            "TXNTYPE" => "SALE",
            "HASH" => $checksum
        );

		  return $params_data;
    }

    protected function _debug($debugData)
    {
        if (method_exists($this, 'getDebugFlag')) {
            return parent::_debug($debugData);
        }

        if ($this->getConfigData('debug')) {
            Mage::log($debugData, null, 'payment_' . $this->getCode() . '.log', true);
        }
    }
}