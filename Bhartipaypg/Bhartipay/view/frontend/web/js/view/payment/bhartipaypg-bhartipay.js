define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'bhartipay',
                component: 'Bhartipaypg_Bhartipay/js/view/payment/method-renderer/bhartipaypg-bhartipay'
            }
        );
        return Component.extend({});
    }
 );