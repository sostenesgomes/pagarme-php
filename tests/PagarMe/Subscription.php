<?php

class PagarMe_SubscriptionTest extends PagarMeTestCase {

	public function testCreate() {
		$subscription = self::createTestSubscription();	
		$subscription->create();
		$this->assertTrue($subscription->getId());
	}

	public function testCreateWithFraud() {
		$subscription =  new PagarMe_Subscription(array(
			'amount' => 2000,
			'customer_email' => "customer@pagar.me",
			'payment_method' => "credit_card",		
			'postback_url' => 'http://testepagarme.com',
			'card_number' => '4111111111111111',
			'card_holder_name' => "Jose da Silva",
			'card_expiracy_month' => "12",
			'card_expiracy_year' => '15',
			'card_cvv' => "123",
			'customer' => array(
				'name' => "Jose da Silva",  
				'document_number' => "36433809847", 
				'document_type' => 'cpf', 
				'email' => "henrique@pagar.me", 
				'address' => array(
					'street' => 'Av. Brigadeiro Faria Lima', 
					'city' => 'São Paulo', 
					'state' => 'SP', 
					'neighborhood' => 'Itaim bibi',
					'zipcode' => '01452000', 
					'street_number' => 2941, 
				),
				'phone' => array(
					'type' => 'cellphone', 
					'ddd' => 12, 
					'number' => '981433533', 
				),
				'sex' => 'M', 
				'born_at' => '0')
			));

		// curl -X POST https://api.pagar.me/1/subscriptions -d postback_url='teste.pagar.me' -d 'payment_method=credit_card' -d 'card_expiracy_date=1115' -d 'amount=70000' -d card_cvv='123' -d 'card_number=4901720080344448' -d card_holder_name='Jose da silva' -d 'customer[name]=Jose Silva' -d 'customer[document_number]=36433809847' -d 'customer[document_type]=cpf' -d customer_email='henrique@pagar.me' -d 'customer[email]=henrique@pagar.me' -d 'customer[address][street]=Av Brigadeiro Faria Lima' -d 'customer[address][city]=São Paulo' -d 'customer[address][state]=SP' -d 'customer[address][neighborhood]=Itaim bibi' -d 'customer[address][zipcode]=01452000' -d 'customer[address][street_number]=2941' -d 'customer[phone][type]=cellphone' -d 'customer[phone][ddd]=12' -d 'customer[phone][number]=981433533' -d 'api_key=xG5CDo48nzL3wwhebSXnBdXML3yzAl' -d 'customer[address][country]=Brasil' -d 'customer[phone][ddi]=55' -d 'customer[sex]=M'
		$subscription->create();
		$this->assertTrue($subscription->getId());
		$this->assertTrue($subscription->getCustomer());
		$this->assertTrue($subscription->getTransactions());

		$this->assertTrue($subscription->getCustomer());

		$this->assertTrue($subscription->getCustomer()->getPhones());
		$this->assertTrue($subscription->getCustomer()->getAddresses());

		$this->assertTrue($subscription->getCustomer()->getName());
		$this->assertTrue($subscription->getCustomer()->getDocumentNumber());
		$this->assertTrue($subscription->getCustomer()->getDocumentType());
		$this->assertTrue($subscription->getCustomer()->getEmail());
		$this->assertTrue($subscription->getCustomer()->getSex());
		$this->assertTrue($subscription->getCustomer()->getId());


	}

	public function testCreateWithPlanAndFraud() {
		$subscription =  new PagarMe_Subscription(array(
			'amount' => 2000,
			'customer_email' => "customer@pagar.me",
			'payment_method' => "credit_card",		
			'postback_url' => 'http://testepagarme.com',
			'card_number' => '4111111111111111',
			'card_holder_name' => "Jose da Silva",
			'card_expiracy_month' => "12",
			'card_expiracy_year' => '15',
			'card_cvv' => "123",
			'customer' => array(
				'name' => "Jose da Silva",  
				'document_number' => "36433809847", 
				'document_type' => 'cpf', 
				'email' => "henrique@pagar.me", 
				'address' => array(
					'street' => 'Av. Brigadeiro Faria Lima', 
					'city' => 'São Paulo', 
					'state' => 'SP', 
					'neighborhood' => 'Itaim bibi',
					'zipcode' => '01452000', 
					'street_number' => 2941, 
				),
				'phone' => array(
					'type' => 'cellphone', 
					'ddd' => 12, 
					'number' => '981433533', 
				),
				'sex' => 'M', 
				'born_at' => '0')
		));
		$plan = self::createTestPlan();
		$plan->create();
		$subscription->setPlan($plan);

		// curl -X POST https://api.pagar.me/1/subscriptions -d postback_url='teste.pagar.me' -d 'payment_method=credit_card' -d 'card_expiracy_date=1115' -d 'amount=70000' -d card_cvv='123' -d 'card_number=4901720080344448' -d card_holder_name='Jose da silva' -d 'customer[name]=Jose Silva' -d 'customer[document_number]=36433809847' -d 'customer[document_type]=cpf' -d customer_email='henrique@pagar.me' -d 'customer[email]=henrique@pagar.me' -d 'customer[address][street]=Av Brigadeiro Faria Lima' -d 'customer[address][city]=São Paulo' -d 'customer[address][state]=SP' -d 'customer[address][neighborhood]=Itaim bibi' -d 'customer[address][zipcode]=01452000' -d 'customer[address][street_number]=2941' -d 'customer[phone][type]=cellphone' -d 'customer[phone][ddd]=12' -d 'customer[phone][number]=981433533' -d 'api_key=xG5CDo48nzL3wwhebSXnBdXML3yzAl' -d 'customer[address][country]=Brasil' -d 'customer[phone][ddi]=55' -d 'customer[sex]=M'
		$subscription->create();
		$this->assertTrue($subscription->getId());
		$this->assertTrue($subscription->getCustomer());
		$this->assertTrue($subscription->getTransactions());
		$this->assertTrue($subscription->getPlan()->getId());
		$this->assertTrue($plan->getId());

		$subscription2 = PagarMe_Subscription::findById($subscription->getId());
		$this->assertTrue($subscription2->getPlan());
		$this->assertEqual($subscription2->getPlan()->getId(), $plan->getId());
	}

	public function testCreateWithPlan() {
		$plan = self::createTestPlan();
		$subscription = self::createTestSubscription();
		$plan->create();

		$subscription->setPlan($plan);
		$subscription->create();

		$this->assertTrue($subscription->getPlan()->getId());
		$this->assertTrue($plan->getId());

		$subscription2 = PagarMe_Subscription::findById($subscription->getId());
		$this->assertTrue($subscription2->getPlan());
		$this->assertEqual($subscription2->getPlan()->getId(), $plan->getId());
	}

	public function testCharge() {
		$subscription = self::createTestSubscription();
		$subscription->create();
		$subscription->charge(3600);
		$transactions = $subscription->getTransactions();

		$this->assertEqual($transactions[1]->getAmount(), 3600);
	}
}

?>
