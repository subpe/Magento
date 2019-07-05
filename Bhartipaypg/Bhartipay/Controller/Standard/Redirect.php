<?php

namespace Bhartipaypg\Bhartipay\Controller\Standard;

class Redirect extends \Bhartipaypg\Bhartipay\Controller\Bhartipay
{
    public function execute()
    {
        $promo='';
        if(isset($_GET['promo'])){
            if(trim($_GET['promo'])!=''){
                $promo=$_GET['promo'];
            }
        }
        $order = $this->getOrder();
        if ($order->getBillingAddress())
        {
            $order->setState("pending_payment")->setStatus("pending_payment");
            $order->addStatusToHistory($order->getStatus(), "Customer was redirected to bhartipay.");
            $order->save();
            
            if($promo!=''){
                $order->bhartipayPromoCode=$promo;
            }

            $this->getResponse()->setRedirect(
                $this->getBhartipayModel()->buildBhartipayRequest($order)
            );
        }
        else
        {
            $this->_cancelPayment();
            $this->_bhartipaySession->restoreQuote();
            $this->getResponse()->setRedirect(
                $this->getBhartipayHelper()->getUrl('checkout')
            );
        }
    }
}