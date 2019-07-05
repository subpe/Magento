<?php

namespace Bhartipaypg\Bhartipay\Controller\Standard;

class Cancel extends \Bhartipaypg\Bhartipay\Controller\Bhartipay
{

    public function execute()
    {
        $this->_cancelPayment();
        $this->_checkoutSession->restoreQuote();
        $this->getResponse()->setRedirect(
            $this->getBhartipayHelper()->getUrl('checkout')
        );
    }

}
