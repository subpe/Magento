<?php

namespace Subpepg\Subpe\Controller\Standard;

class Redirect extends \Subpepg\Subpe\Controller\Subpe
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
            $order->addStatusToHistory($order->getStatus(), "Customer was redirected to subpe.");
            $order->save();
            
            if($promo!=''){
                $order->subpePromoCode=$promo;
            }

            $this->getResponse()->setRedirect(
                $this->getSubpeModel()->buildSubpeRequest($order)
            );
        }
        else
        {
            $this->_cancelPayment();
            $this->_subpeSession->restoreQuote();
            $this->getResponse()->setRedirect(
                $this->getSubpeHelper()->getUrl('checkout')
            );
        }
    }
}