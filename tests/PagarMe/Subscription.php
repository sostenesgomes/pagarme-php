<?php

class PagarMe_SubscriptionTest extends PagarMeTestCase {

	public function testCreate() {
		$subscription = self::createTestSubscription();	
		$subscription->create();
		$this->assertTrue($subscription->getId());
	}

	public function testCreateWithPlan() {
		$subscription = self::createTestSubscription();
		$plan = self::createTestPlan();
		// $plan->create();
		// $subscription->setPlan($plan);
		$subscription->create();
		// $subscription2 = PagarMe_Subscription::findById($subscription->getId());
		// $this->assertEqual($subscription2->getPlan()->getId(), $plan->getId());
	}
}

?>
