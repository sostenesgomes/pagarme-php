<?php

class PagarMe_TransactionTest extends PagarMeTestCase {

	public function testCharge() {
		$transaction = self::createTestTransaction();
		$this->assertFalse($transaction->getId());
		$transaction->charge();
		$this->assertTrue($transaction->getId());
	}

	public function testCreationWithFraud() {
		authorizeFromEnv();
		self::setAntiFraud("false");
		$transaction = new PagarMe_Transaction(array('amount' => 70000, 'card_number' => '4901720080344448', 'card_holder_name' => "Jose da silva", 
			'card_expiracy_month' => 11, 'card_expiracy_year' => "13", 'card_cvv' => 356, 'name' => "Jose da Silva",  
			'document_number' => "36433809847", 'document_type' => 'cpf', 'email' => "henrique@pagar.me", 
			'street' => 'Av. Brigadeiro Faria Lima', 'city' => 'São Paulo', 'state' => 'SP', 'neighborhood' => 'Itaim bibi',
			'zipcode' => '01452000', 'street_number' => 2941, 'phone_type' => 'cellphone', 'ddd' => 12, 'number' => '981433533', 
			'sex' => 'M', 'born_at' => '0'));
		
		$transaction->charge();
		$this->assertTrue($transaction->getId());
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
}

?>
