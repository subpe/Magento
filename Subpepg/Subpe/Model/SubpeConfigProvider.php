<?php

namespace Subpepg\Subpe\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\UrlInterface as UrlInterface;

class SubpeConfigProvider implements ConfigProviderInterface
{
    protected $methodCode = "subpe";

    protected $method;
    
    protected $urlBuilder;

    public function __construct(PaymentHelper $paymentHelper, UrlInterface $urlBuilder) {
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
        $this->urlBuilder = $urlBuilder;
    }

    public function getConfig()
    {
        $promo=$this->method->getConfigData("promo_code");
        $hide_promo_field=$this->method->getConfigData("hide_promo_field");
        $promo_code_local_validation=$this->method->getConfigData("promo_code_local_validation");
        return $this->method->isAvailable() ? [
            'payment' => [
                'subpe' => [
                    'redirectUrl' => $this->urlBuilder->getUrl('subpe/Standard/Redirect', ['_secure' => true]),
                    'promoCode'=>$promo,
                    'hide_promo_field'=>$hide_promo_field,
                    'promo_code_local_validation'=>$promo_code_local_validation
                ]
            ]
        ] : [];
    }

    protected function getRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('paypal/ipn/');
    }
    
    protected function getFormData()
    {
        return $this->method->getRedirectUrl();
    }
}
