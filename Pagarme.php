<?php

if (!function_exists('curl_init')) {
	throw new Exception('PagarMe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('PagarMe needs the JSON PHP extension.');
}


function __autoload($class){

	$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "Pagarme" . DIRECTORY_SEPARATOR;
	
	$file = $dir . $class . ".php";
		
	if (file_exists($file)){
		require_once($file);
		return;
	}elseif (strstr($class, "PagarMe_")) {
		$file = $dir . str_replace("PagarMe_", "", $class) . ".php";
		
		if (file_exists($file)){
			require_once($file);
			return;		
		}else{
			throw new Exception("Unable to load" .$class);
		}

	}else{
		throw new Exception("Unable to load" .$class);
	}
}

/*
require(dirname(__FILE__) . '/lib/Pagarme/Pagarme.php');
require(dirname(__FILE__) . '/lib/Pagarme/Error.php');
require(dirname(__FILE__) . '/lib/Pagarme/Exception.php');
require(dirname(__FILE__) . '/lib/Pagarme/ApiException.php');
require(dirname(__FILE__) . '/lib/Pagarme/RestClient.php');
require(dirname(__FILE__) . '/lib/Pagarme/Request.php');
require(dirname(__FILE__) . '/lib/Pagarme/model.php');
require(dirname(__FILE__) . '/lib/Pagarme/TransactionCommon.php');
require(dirname(__FILE__) . '/lib/Pagarme/Transaction.php');
require(dirname(__FILE__). '/lib/Pagarme/Plan.php');
require(dirname(__FILE__) . '/lib/Pagarme/Subscription.php');
require(dirname(__FILE__) . '/lib/Pagarme/Customer.php');
require(dirname(__FILE__) . '/lib/Pagarme/Address.php');
require(dirname(__FILE__) . '/lib/Pagarme/Phone.php');
*/

?>
