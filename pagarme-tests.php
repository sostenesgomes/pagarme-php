<?php

// 
// 
require 'lib/Pagarme.php';

$apiKey = "vbNY7x1rxHTNQRfYGAZzdjhcUwxUQO";
PagarMe::setApiKey($apiKey);

$subscription = new PagarMe_Subscription(array(
	'amount' => 2000,
	'customer_email' => "customer@pagar.me",
	'payment_method' => "credit_card",		
	'postback_url' => 'http://testepagarme.com',
	'card_number' => '4111111111111111',
	'card_holder_name' => "Jose da Silva",
	'card_expiracy_month' => "12",
	'card_expiracy_year' => '15',
	'card_cvv' => "123"
));

$subscription->create();
var_dump($subscription);


?>
