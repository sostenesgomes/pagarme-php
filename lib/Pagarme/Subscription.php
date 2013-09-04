<?php
class PagarMe_Subscription extends PagarMe_TransactionCommon {
	protected $plan, $current_period_start, $current_period_end, $customer_email, $transactions;	

	public function __construct($first_parameter = 0, $server_response = 0) { 
		$this->customer_email = ($first_parameter['customer_email']) ? $first_parameter['customer_email'] : '';
		$this->payment_method = ($first_parameter['payment_method']) ? $first_parameter['payment_method'] : 'credit_card';
		$this->postback_url = ($first_parameter['postback_url']) ? $first_parameter['postback_url'] : ''; 
		$this->plan = ($first_parameter['plan']) ? $first_parameter['plan'] : null;
		$this->amount = $first_parameter["amount"] ? $first_parameter['amount'] : '';
		$this->card_number = ($first_parameter["card_number"]) ? $first_parameter['card_number']  : '';
		$this->card_holder_name = ($first_parameter["card_holder_name"]) ? $first_parameter['card_holder_name'] : '';
		$this->card_expiracy_month = ($first_parameter["card_expiracy_month"]) ? $first_parameter['card_expiracy_month'] : '';
		$this->card_expiracy_year = ($first_parameter["card_expiracy_year"]) ? $first_parameter['card_expiracy_year'] : '';
		$this->card_cvv = $first_parameter["card_cvv"] ? $first_parameter['card_cvv'] : '';
		$this->transactions = Array();

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
		if($r['plan']) {
			$this->plan = new PagarMe_Plan(0, $r['plan']);
		}	
		if($r['transactions']) {
			foreach($r['transactions'] as $transaction) {
				$this->transactions[] = new PagarMe_Transaction(0, $transaction);
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
