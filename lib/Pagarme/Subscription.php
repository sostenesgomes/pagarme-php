<?php
class PagarMe_Subscription extends PagarMe_TransactionCommon {
	protected $plan, $current_period_start, $current_period_end, $customer_email, $transactions;	

	public function __construct($subscription = 0) { 
		$this->transactions = Array();
		$this->updateFieldsFromResponse($subscription);
	}

	public function create() {
		try {
			$validation_error = $this->card_hash ? null : $this->errorInTransaction();
			if($validation_error) {
				throw new Exception($validation_error);
			}

			if($this->id) {
				throw new Exception("Subscription já criada");
			}

			$request = new PagarMe_Request(self::getUrl(),'POST');
			$parameters = array(
				'amount' => $this->amount,
				'payment_method' => $this->payment_method,
				'card_hash' => ($this->payment_method == 'credit_card') ? ($this->card_hash ? $this->card_hash : $this->generateCardHash()) : null,
				'postback_url' => ($this->postback_url),
				'customer_email' => $this->customer_email
			);


			if($this->plan) {
				$parameters['plan_id'] = $this->plan->getId();
			}

			if($this->checkCustomerInformation()) {
				$parameters =  $this->mergeCustomerInformation($parameters);
			}

			$request->setParameters($parameters);
			$response = $request->run();
			$this->updateFieldsFromResponse($response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function charge($amount) {
		try {
			if($this->plan) {
				throw new Exception("Subscription nao eh variavel.");
			}
			$this->setAmount($amount);
			$request = new PagarMe_Request(self::getUrl(). '/' . $this->id, 'POST');
			$request->setParameters(array('amount' => $this->getAmount()));
			$response = $request->run();
			$this->updateFieldsFromResponse($response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function updateFieldsFromResponse($r) {
		parent::updateFieldsFromResponse($r);
		$this->customer_email = ($r['customer_email']) ? $r['customer_email'] : 0;
		$this->current_period_start = ($r['current_period_start']) ? $r['current_period_start'] : 0;
		$this->current_period_end = ($r['current_period_end']) ? $r['current_period_end'] : 0;
		if($r['plan']) {
			$this->plan = new PagarMe_Plan($r['plan']);
		}	
		if($r['transactions']) {
			for($i=0; $i < sizeof($r['transactions']); $i++) {
				$this->transactions[$i] = new PagarMe_Transaction($r['transactions'][$i]);
			}
		}
	}

	public function getPlan() {
		return $this->plan;
	}

	public function setPlan($plan) {
		$this->plan = $plan;
	}

	public function getTransactions(){
		return $this->transactions;
	}

	public function setTransactions($transactions) {
		$this->transactions = $transactions;
	}
}
?>
