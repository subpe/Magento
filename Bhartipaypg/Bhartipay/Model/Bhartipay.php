<?php

namespace Bhartipaypg\Bhartipay\Model;

use Bhartipaypg\Bhartipay\Helper\Data as DataHelper;

class Bhartipay extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'bhartipay';
    protected $_code = self::CODE;
    protected $_isInitializeNeeded = true;
    protected $_isGateway = true;
    protected $_isOffline = true;
    protected $helper;
    protected $_minAmount = null;
    protected $_maxAmount = null;
    protected $_supportedCurrencyCodes = array('INR');
    protected $_formBlockType = 'Bhartipaypg\Bhartipay\Block\Form\Bhartipay';
    protected $_infoBlockType = 'Bhartipaypg\Bhartipay\Block\Info\Bhartipay';
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Bhartipaypg\Bhartipay\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

        $this->_minAmount = "0.50";
        $this->_maxAmount = "1000000";
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Instantiate state and set it to state object.
     *
     * @param string                        $paymentAction
     * @param \Magento\Framework\DataObject $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);      
    
        $stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        if ($quote && (
                $quote->getBaseGrandTotal() < $this->_minAmount
                || ($this->_maxAmount && $quote->getBaseGrandTotal() > $this->_maxAmount))
        ) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    public function buildBhartipayRequest($order)
    {
        $callBackUrl=$this->urlBuilder->getUrl('bhartipay/Standard/Response', ['_secure' => true]);
        if($this->getConfigData("custom_callbackurl")=='1'){
            $callBackUrl=$this->getConfigData("callback_url")!=''?$this->getConfigData("callback_url"):$callBackUrl;
        }
        $params = array('MID' => $this->getConfigData("MID"),               
                        'TXN_AMOUNT' => round($order->getGrandTotal(), 2),
                        'CHANNEL_ID' => $this->getConfigData("Channel_Id"),
                        'INDUSTRY_TYPE_ID' => $this->getConfigData("Industry_id"),
                        'WEBSITE' => $this->getConfigData("Website"),
                        'CUST_ID' => $order->getCustomerEmail(),
                        'ORDER_ID' => $order->getRealOrderId(),                     
                        'EMAIL' => $order->getCustomerEmail(),
                        'CALLBACK_URL' => $callBackUrl);  

    $post_group_data=array();
    $customer_name=$order->getCustomerName();
    $hash = $this->helper->getHashFromArray($params, $this->getConfigData("merchant_key"), $this->getConfigData("MID"),$customer_name);
    $params['HASH'] = $hash;
    $post_group_data['PAY_ID'] =$this->getConfigData("MID");
    $post_group_data['ORDER_ID'] = $order->getRealOrderId();
    $post_group_data['RETURN_URL'] = $callBackUrl;
    $post_group_data['CUST_EMAIL'] = $order->getCustomerEmail();
    $post_group_data['CUST_NAME'] = "$customer_name";
    $post_group_data['CUST_STREET_ADDRESS1'] = '';
    $post_group_data['CUST_CITY'] = '';
    $post_group_data['CUST_STATE'] = '';
    $post_group_data['CUST_COUNTRY']= '';
    $post_group_data['CUST_ZIP']='';
    $post_group_data['CUST_PHONE']='';
    $post_group_data['CURRENCY_CODE']= 356;
    $post_group_data['AMOUNT']=round($order->getGrandTotal(), 2)*100;
    $post_group_data['PRODUCT_DESC']='';
    $post_group_data['CUST_SHIP_STREET_ADDRESS1']='';
    $post_group_data['CUST_SHIP_CITY']='';
    $post_group_data['CUST_SHIP_STATE']=''; 
    $post_group_data['CUST_SHIP_COUNTRY']='';
    $post_group_data['CUST_SHIP_ZIP']='';
    $post_group_data['CUST_SHIP_PHONE']='';
    $post_group_data['CUST_SHIP_NAME']='';
    $post_group_data['TXNTYPE']='SALE';
      $post_group_data['HASH']="$hash";
        if(isset($order->bhartipayPromoCode)){
            $params['PROMO_CAMP_ID']=$order->bhartipayPromoCode;
        }
        
        $url = $this->getConfigData('transaction_url')."?";
        $urlparam = "";
        foreach($post_group_data as $key => $val){
            $urlparam = $urlparam.$key."=".$val."&";
        }
        $url = $url . $urlparam;
        return $url;
    }

    public function autoInvoiceGen()
    {
        $result = $this->getConfigData("payment_action");            
        return $result;
    }

    public function getRedirectUrl()
    {
        $url = $this->getConfigData('transaction_url');
        return $url;
    }
    
    public function getStatusQueryUrl()
    {
        $url = $this->getConfigData('transaction_status_url');
        return $url;
    }
    
    public function getNewStatusQueryUrl()
    {
        $url = $this->getConfigData('transaction_status_url');
        return $url;
    }

    public function getReturnUrl()
    {
        
    }

    public function getCancelUrl()
    {
        
    }
}
