# Magento
This plugin belongs to Bhartipay payment gateway.

Magento v2.x
This is a Magento 2.0 v  which is supported by Magento version 2.0.x onward. 


Installation and Configuration upload app/code/Bhartipaypg (all files and folder) at you server end.

Run below command: php bin/magento module:enable Bhartipaypg_Bhartipay php bin/magento setup:upgrade php bin/magento setup:static-content:deploy

goto Admin->Store->Configuration->Sales->Payment Method->Bhartipay fill details here and save them.

goto Admin->System->Cache Management Clear all Cache.

Now you can collect payment via Bhartipay .


Go to Store/Configuration/Sales/Payment Methods/Bhartipay

Fill all the required details:

1)Enable:yes

2)Title:Bhartipay

3)Merchant Id:Pay Id { Provided by Bhartipay }

4)Merchant Key:Salt { Provided by Bhartipay }

5)Custom Callback Url:no

6)Transaction Url: For Test:https://uat.bhartipay.com/crm/jsp/paymentrequest
                 : For Live:https://merchant.bhartipay.com/crm/jsp/paymentrequest

7)Industry Type Id: Name of your choice

8)Website:Your website Name
