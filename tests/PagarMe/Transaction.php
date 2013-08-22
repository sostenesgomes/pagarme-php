<?php

class PagarMe_TransactionTest extends PagarMeTestCase {

	public function testCharge() {
		$transaction = self::createTestTransaction();
		$this->assertFalse($transaction->getId());
		$transaction->charge();
		$this->assertTrue($transaction->getId());
	}

	public function testChargeback() {
		$transaction = self::createTestTransaction();
		$transaction->charge();
		$transaction->chargeback();
		$this->assertEqual($transaction->getStatus(), 'chargebacked');
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
