<?php

namespace Subpepg\Subpe\Controller\Standard;

class Cancel extends \Subpepg\Subpe\Controller\Subpe
{

    public function execute()
    {
        $this->_cancelPayment();
        $this->_checkoutSession->restoreQuote();
        $this->getResponse()->setRedirect(
            $this->getSubpeHelper()->getUrl('checkout')
        );
    }

}
