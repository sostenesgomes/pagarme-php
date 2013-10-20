<?php

class PagarMe_PlanTest extends PagarMeTestCase {
	public function testCreate() {
		$plan = self::createTestPlan();
		$plan->create();
		$this->assertTrue($plan->getId());
	}

	public function testUpdate() {
		$plan = self::createTestPlan();
		$plan->create();
		$this->assertEqual($plan->getName(), "Plano Silver");
		$plan->setName("Plano gold!");
		$plan->update();
		$plan2 = PagarMe_Plan::findById($plan->getId());		
		$this->assertEqual($plan->getName(), $plan2->getName());

		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$plan2->setAmount('R$ 20.00');
		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$plan2->setDays('60');
	} 

	public function testValidate() {
		$plan = new PagarMe_Plan();

		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$plan->setAmount('0');
		$plan->create();
		$plan->setAmount('10000');

		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$plan->days('0');
		$plan->create();
		$plan->setDays('30');

		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$plan->setTrialDays('-1');
		$plan->create();
		$plan->setTrialDays("10");

		$this->expectException(new IsAExpectation('PagarMe_Exception'));
		$plan->setName('');
		$plan->create();
		$plan->setName('Plan');

		$plan->create();

		$plan->assertTrue($plan->getId());
	}
}

?>
