<?php

if (!function_exists('curl_init')) {
	throw new Exception('PagarMe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('PagarMe needs the JSON PHP extension.');
}


require(dirname(__FILE__) . '/Pagarme/Pagarme.php');
require(dirname(__FILE__) . '/Pagarme/Exception.php');
require(dirname(__FILE__) . '/Pagarme/TransactionException.php');
require(dirname(__FILE__) . '/Pagarme/ApiException.php');
require(dirname(__FILE__) . '/Pagarme/RestClient.php');
require(dirname(__FILE__) . '/Pagarme/Request.php');
require(dirname(__FILE__) . '/Pagarme/Model.php');
require(dirname(__FILE__) . '/Pagarme/TransactionCommon.php');
require(dirname(__FILE__) . '/Pagarme/Transaction.php');
require(dirname(__FILE__). '/Pagarme/Plan.php');
require(dirname(__FILE__) . '/Pagarme/Subscription.php');





?>
