<?php
//contains utility functions for encryption decrytion

class Bpay_bhartipay_Helper_Data extends Mage_Payment_Helper_Data
{
    public function getPendingPaymentStatus()
    {
        if (version_compare(Mage::getVersion(), '1.4.0', '<')) {
            return Mage_Sales_Model_Order::STATE_HOLDED;
        }
        return Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
    }
	
	function pkcs5_pad_e($text, $blocksize) 
	{
	$pad = $blocksize - (strlen($text) % $blocksize);
	return $text . str_repeat(chr($pad), $pad);
	}
	
	function encrypt_e($input, $ky) {
		$key   = html_entity_decode($ky);
		$iv = "@@@@&&&&####$$$$";
		$data = openssl_encrypt ( $input , "AES-128-CBC" , $key, 0, $iv );
		return $data;
	}

	function decrypt_e($crypt, $ky) {
		$key   = html_entity_decode($ky);
		$iv = "@@@@&&&&####$$$$";
		$data = openssl_decrypt ( $crypt , "AES-128-CBC" , $key, 0, $iv );
		return $data;
	}


	function generateSalt_e($length) {
	$random = "";
	srand((double) microtime() * 1000000);

	 $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
	$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
	$data .= "0FGH45OP89";

	for ($i = 0; $i < $length; $i++) {
		$random .= substr($data, (rand() % (strlen($data))), 1);
	}

	return $random;
	}

function checkString_e($value) {
	$myvalue = ltrim($value);
	$myvalue = rtrim($myvalue);
	if ($myvalue == 'null')
		$myvalue = '';
	return $myvalue;
}

function getChecksumFromArray($arrayList, $key,$custname) {
	    
	 $arrayList['TXN_AMOUNT'];
	//previous data
 	$postdata=$arrayList;

 	$price = round($arrayList['TXN_AMOUNT'])*100;
$post_group_data = array(
			"PAY_ID" =>$arrayList['MID'],
			"ORDER_ID" => $arrayList['ORDER_ID'],
			"RETURN_URL" => $arrayList['CALLBACK_URL'],
			"CUST_EMAIL" => $arrayList['EMAIL'] ,
			"CUST_NAME" => $custname,
			"CUST_STREET_ADDRESS1" => "",
			"CUST_CITY" =>"" ,
			"CUST_STATE" =>"",
			"CUST_COUNTRY" =>"" ,
			"CUST_ZIP" => "",
			"CUST_PHONE" => "Nan" ,
			"CURRENCY_CODE" => 356,
			"AMOUNT" => $price,
			"PRODUCT_DESC" => "Payment" ,
			"CUST_SHIP_STREET_ADDRESS1" =>"",
			"CUST_SHIP_CITY" =>"",
			"CUST_SHIP_STATE" =>"",
			"CUST_SHIP_COUNTRY" => "",
			"CUST_SHIP_ZIP" =>"",
			"CUST_SHIP_PHONE" => "",
			"CUST_SHIP_NAME" => "",
			"TXNTYPE" => "SALE" ,
			 );


   ksort($post_group_data);

	$all = '';
        foreach ($post_group_data as $name => $value) {
            $all .= $name."=".$value."~";
        }
        $all = substr($all, 0, -1);
        $all.=$key;

         return strtoupper(hash('sha256', $all));
}

function verifychecksum_e($arrayList, $key, $checksumvalue) {
	$arrayList = Mage::helper('bhartipay')->removeCheckSumParam($arrayList);
	ksort($arrayList);
	$str = Mage::helper('bhartipay')->getArray2StrForVerify($arrayList);
	$bhartipay_hash = Mage::helper('bhartipay')->decrypt_e($checksumvalue, $key);
	$salt = substr($bhartipay_hash, -4);

	$finalString = $str . "|" . $salt;

	$website_hash = hash("sha256", $finalString);
	$website_hash .= $salt;

	$validFlag = "FALSE";
	if ($website_hash == $bhartipay_hash) {
		$validFlag = "TRUE";
	} else {
		$validFlag = "FALSE";
	}
	return $validFlag;
}

function getArray2StrForVerify($arrayList) {
	$paramStr = "";
	$flag = 1;
	foreach ($arrayList as $key => $value) {
		if ($flag) {
			$paramStr .= Mage::helper('bhartipay')->checkString_e($value);
			$flag = 0;
		} else {
			$paramStr .= "|" . Mage::helper('bhartipay')->checkString_e($value);
		}
	}
	return $paramStr;
}
function getArray2Str($arrayList) {
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
			$paramStr .= Mage::helper('bhartipay')->checkString_e($value);
			$flag = 0;
		} else {
			$paramStr .= "|" . Mage::helper('bhartipay')->checkString_e($value);
		}
	}
	return $paramStr;
}

function redirect2PG($paramList, $key) {
	$hashString = Mage::helper('bhartipay')->getchecksumFromArray($paramList);
	$checksum = Mage::helper('bhartipay')->encrypt_e($hashString, $key);
}

function removeCheckSumParam($arrayList) {
	if (isset($arrayList["CHECKSUMHASH"])) {
		unset($arrayList["CHECKSUMHASH"]);
	}
	return $arrayList;
}
function callAPI($apiURL, $requestParamList)
{
    $jsonResponse      = "";
    $responseParamList = array();
    $JsonData          = json_encode($requestParamList);
    $postData          = 'JsonData=' . urlencode($JsonData);
    $ch                = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ));
    $jsonResponse      = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}
function callNewAPI($apiURL, $requestParamList)
{
    $jsonResponse      = "";
    $responseParamList = array();
    $JsonData          = json_encode($requestParamList);
    $postData          = 'JsonData=' . urlencode($JsonData);
    $ch                = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ));
    $jsonResponse      = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}
}
