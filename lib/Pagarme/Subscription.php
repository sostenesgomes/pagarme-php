<?php
class PagarMe_Subscription extends PagarMe_TransactionCommon {
	protected $plan, $current_period_start, $current_period_end, $customer_email;	

	public function __construct($first_parameter = 0, $server_response = 0) { 
		self::$root_url = '/subscriptions';
		$this->customer_email = ($first_parameter['customer_email']) ? $first_parameter['customer_email'] : '';
		$this->payment_method = ($first_parameter['payment_method']) ? $first_parameter['payment_method'] : 'credit_card';
		$this->postback_url = ($first_parameter['postback_url']) ? $first_parameter['postback_url'] : ''; 
		$this->plan = ($first_parameter['plan']) ? $first_parameter['plan'] : '';
		$this->amount = $first_parameter["amount"] ? $first_parameter['amount'] : '';
		$this->card_number = ($first_parameter["card_number"]) ? $first_parameter['card_number']  : '';
		$this->card_holder_name = ($first_parameter["card_holder_name"]) ? $first_parameter['card_holder_name'] : '';
		$this->card_expiracy_month = ($first_parameter["card_expiracy_month"]) ? $first_parameter['card_expiracy_month'] : '';
		$this->card_expiracy_year = ($first_parameter["card_expiracy_year"]) ? $first_parameter['card_expiracy_year'] : '';
		$this->card_cvv = $first_parameter["card_cvv"] ? $first_parameter['card_cvv'] : '';

		if($server_response) {
			$this->updateFieldsFromResponse($server_response);
		}
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

			$request = new PagarMe_Request(self::$root_url, 'POST');
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
			$request = PagarMe_Request(self::$root_url . '/' . $this->id, 'POST');
			$request->setParameters(array('amount' => $this->amount));
			$response = $request->run();
			$this->updateFieldsFromResponse($response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function updateFieldsFromResponse($r) {
		parent::updateFieldsFromResponse($r);
		if($r['plan_id']) {
			$this->plan = PagarMe_Plan::findById($r['plan_id']);
		}	
	}

	public function getPlan() {
		return $this->plan;	
	}

	public function setPlan($plan) {
		$this->plan = $plan;
	}
}
?>
