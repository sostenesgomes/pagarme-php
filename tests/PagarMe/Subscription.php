<?php

class PagarMe_SubscriptionTest extends PagarMeTestCase {

	public function testCreate() {
		$subscription = self::createTestSubscription();	
		$subscription->create();
		$this->assertTrue($subscription->getId());
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
