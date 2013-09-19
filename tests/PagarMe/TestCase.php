<?php

abstract class PagarMeTestCase extends UnitTestCase {
	

	protected static function setAntiFraud($status) {
		authorizeFromEnv();	
		$request = new PagarMe_Request('/company', 'PUT');
		$request->setParameters(array('antifraud' => $status));	
		$response = $request->run();
	}

	protected static function createTestTransaction(array $attributes = array()) 
	{
		authorizeFromEnv();	
		self::setAntiFraud("false");
		return new PagarMe_Transaction(
			$attributes + 
			array(
			"amount" => 1000,
			"card_number" => "4111111111111111",
			"card_holder_name" => "Jose da Silva",
			"card_expiracy_month" => 12,
			"card_expiracy_year" => 15,
			"card_cvv" => "123",
		));
	}

	protected static function createTestPlan(array $attributes = array()) {
		authorizeFromEnv();		
		return new PagarMe_Plan($attributes +
			array(
				'amount' => 1000,
				'days' => '30',
				'name' => "Plano Silver",
				'trial_days' => '2'	
			)
		);
	}	

	protected static function createTestSubscription(array $attributes = array()) {
		authorizeFromEnv();	
		return new PagarMe_Subscription($attributes + array(
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
	}

}

?>
