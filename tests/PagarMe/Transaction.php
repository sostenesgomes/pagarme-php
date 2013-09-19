<?php

class PagarMe_TransactionTest extends PagarMeTestCase {

// 	public function testCharge() {
// 		$transaction = self::createTestTransaction();
// 		$this->assertFalse($transaction->getId());
// 		$transaction->charge();
// 		$this->assertTrue($transaction->getId());
// 	}
// 
	public function testCreationWithFraud() {
		self::setAntiFraud("true");		
		$transaction = new PagarMe_Transaction(array('amount' => 70000, 'card_number' => '4901720080344448', 'card_holder_name' => "Jose da silva", 
			'card_expiracy_month' => 11, 'card_expiracy_year' => "13", 'card_cvv' => 356, 'name' => "Jose da Silva",  
			'document_number' => "36433809847", 'document_type' => 'cpf', 'email' => "henrique@pagar.me", 
			'street' => 'Av. Brigadeiro Faria Lima', 'city' => 'Sao Paulo', 'state' => 'SP', 'neighborhood' => 'Itaim bibi',
			'zipcode' => '01452000', 'street_number' => 2941, 'phone_type' => 'cellphone', 'ddd' => 12, 'number' => '981433533', 
			'sex' => 'M', 'born_at' => '0'));


		// curl -X POST https://api.pagar.me/1/transactions -d card_expiracy_date='1115' -d 'amount=70000' -d card_cvv='123' -d 'card_number=4901720080344448' -d card_holder_name='Jose da silva' -d 'customer[name]=Jose Silva' -d 'customer[document_number]=36433809847' -d 'customer[document_type]=cpf' -d 'customer[email]=henrique@pagar.me' -d 'customer[address][street]=Av Brigadeiro Faria Lima' -d 'customer[address][city]=São Paulo' -d 'customer[address][state]=SP' -d 'customer[address][neighborhood]=Itaim bibi' -d 'customer[address][zipcode]=01452000' -d 'customer[address][street_number]=2941' -d 'customer[phone][type]=cellphone' -d 'customer[phone][ddd]=12' -d 'customer[phone][number]=981433533' -d 'api_key=xG5CDo48nzL3wwhebSXnBdXML3yzAl' -d 'customer[address][country]=Brasil' -d 'customer[phone][ddi]=55'


		$transaction->charge();
		$this->assertTrue($transaction->getId());
		$this->assertTrue($transaction->getCustomer());
		self::setAntiFraud("false");
	}
// 
// 	public function testRefund() {
// 		$transaction = self::createTestTransaction();
// 		$transaction->charge();
// 		$transaction->refund();
// 		$this->assertEqual($transaction->getStatus(), 'refunded');
// 	}
// 
// 	public function testCreation() {
// 		$transaction = self::createTestTransaction();
// 		$this->assertEqual($transaction->getStatus(), 'local');
// 		$this->assertEqual($transaction->getPaymentMethod(), 'credit_card');
// 	} 
// 
// 	public function testValidation() {
// 		$transaction = new PagarMe_Transaction();
// 		$transaction->setCardNumber("123");
// 		$this->expectException(new IsAExpectation('PagarMe_Exception'));
// 		$transaction->charge();
// 		$transaction->setCardNumber('4111111111111111');
// 
// 		$transaction->setCardHolderName('');
// 		$this->expectException(new IsAExpectation('PagarMe_Exception'));
// 		$transaction->charge();
// 		$transaction->setCardHolderName("Jose da silva");
// 
// 		$transaction->setExpiracyMonth(13);
// 		$this->expectException(new IsAExpectation('PagarMe_Exception'));
// 		$transaction->charge();
// 		$transaction->setExpiracyMonth(12);
// 
// 		$transaction->setExpiracyYear(10);
// 		$this->expectException(new IsAExpectation('PagarMe_Exception'));
// 		$transaction->charge();
// 		$transaction->setExpiracyYear(16);
// 
// 		$transaction->setCvv(123456);
// 		$this->expectException(new IsAExpectation('PagarMe_Exception'));
// 		$transaction->charge();
// 		$transaction->setCvv(123);
// 
// 		$transaction->setAmount(0);
// 		$this->expectException(new IsAExpectation('PagarMe_Exception'));
// 		$transaction->charge();
// 		$transaction->setAmount(1000);
// 	}
}

?>
