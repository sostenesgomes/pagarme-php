<?php

class PagarMe_TransactionTest extends PagarMeTestCase {

	public function testCharge() {
		$transaction = self::createTestTransaction();
		$this->assertFalse($transaction->getId());
		$transaction->charge();
		$this->assertTrue($transaction->getId());

	}

	public function testTransactionWithBoleto() {
		authorizeFromEnv();
		$transaction = new PagarMe_Transaction(array(
			'payment_method' => 'boleto',
			'amount' => '10000',
			'postback_url' => 'testeee.com'
		));

		$transaction->charge();

		$this->assertFalse($transaction->getBoletoUrl());
		$this->assertEqual($transaction->getStatus(), 'waiting_payment');
	}

	public function testCreationWithFraud() {
		authorizeFromEnv();
		self::setAntiFraud("false");
		$transaction = new PagarMe_Transaction(array(
			'card_number' => '4111111111111111', 
			'card_holder_name' => "Jose da silva", 
			'card_expiracy_month' => 11, 
			'card_expiracy_year' => "2013", 
			'card_cvv' => 356, 
			'customer' => array(
				'name' => "Jose da Silva",  
				'document_number' => "36433809847", 
				'email' => "henrique@pagar.me", 
				'address' => array(
					'street' => "Av Faria Lima",
					'neighborhood' => 'Jardim Europa',
					'zipcode' => '12460000', 
					'street_number' => 295, 
				),
				'phone' => array(
					'type' => "cellphone",
					'ddd' => 12, 
					'number' => '981433533', 
				),
				'sex' => 'M', 
				'born_at' => '1995-10-11')
			));

		$transaction->setInstallments(6); // Número de parcelas
		$transaction->setAmount('10.00'); // Set Amount

		$transaction->charge();
		$this->assertEqual($transaction->getStatus(), 'paid');
		$this->assertEqual($transaction->getAmount(), '1000');

		$this->assertTrue($transaction->getCardBrand());
		$this->assertEqual($transaction->getCardBrand(), 'visa');

		$this->assertEqual($transaction->getInstallments(), 6);

		$this->assertTrue($transaction->getId());
		$this->assertFalse($transaction->getRefuseReason());
		$this->assertTrue($transaction->getCustomer());

		$this->assertTrue($transaction->getCustomer()->getPhones());
		$this->assertTrue($transaction->getCustomer()->getAddresses());

		$this->assertTrue($transaction->getCustomer()->getName());
		$this->assertTrue($transaction->getCustomer()->getDocumentNumber());
		$this->assertTrue($transaction->getCustomer()->getDocumentType());
		$this->assertTrue($transaction->getCustomer()->getEmail());
		$this->assertTrue($transaction->getCustomer()->getSex());
		$this->assertTrue($transaction->getCustomer()->getId());


		self::setAntiFraud("false");
	}

	public function testRefund() {
		$transaction = self::createTestTransaction();
		$transaction->charge();
		$transaction->refund();
		$this->assertEqual($transaction->getStatus(), 'refunded');
	}

	public function testCreation() {
		$transaction = self::createTestTransaction();
		$this->assertEqual($transaction->getStatus(), 'local');
		$this->assertEqual($transaction->getPaymentMethod(), 'credit_card');
	} 

	public function testValidation() {
		$transaction = new PagarMe_Transaction();
		$transaction->setCardNumber("123");
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setCardNumber('4111111111111111');

		$transaction->setCardHolderName('');
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setCardHolderName("Jose da silva");

		$transaction->setExpiracyMonth(13);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setExpiracyMonth(12);

		$transaction->setExpiracyYear(10);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setExpiracyYear(16);

		$transaction->setCvv(123456);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setCvv(123);

		$transaction->setAmount(0);
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$transaction->charge();
		$transaction->setAmount(1000);
	}


	public function testFingerprint() {
		$this->assertTrue(PagarMe::validateFingerprint('13', sha1('13' . '#' . PagarMe::getApiKey())));		
	}
}

?>
