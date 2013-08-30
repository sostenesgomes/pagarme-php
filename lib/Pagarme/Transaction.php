<?php
class PagarMe_Transaction extends PagarMe_TransactionCommon {

	protected $installments;

	private $statuses_codes;

	public function __construct($first_parameter = 0, $server_response = 0) 
	{
		$this->root_url = '/transactions';

		$this->status = 'local';

		$this->installments = 1;

		$this->amount = $this->card_number = $this->card_expicary_month = $this->card_expiracy_year = $this->card_cvv = "";

		$this->postback_url = null;

		$this->payment_method = 'credit_card';

		if(gettype($first_parameter) == "string") {

			$this->card_hash = $first_parameter;
		} elseif(gettype($first_parameter) == "array") {

			$this->amount = $first_parameter["amount"] ? $first_parameter['amount'] : '';
			$this->card_number = ($first_parameter["card_number"]) ? $first_parameter['card_number']  : '';
			$this->card_holder_name = ($first_parameter["card_holder_name"]) ? $first_parameter['card_holder_name'] : '';
			$this->card_expiracy_month = ($first_parameter["card_expiracy_month"]) ? $first_parameter['card_expiracy_month'] : '';
			$this->card_expiracy_year = ($first_parameter["card_expiracy_year"]) ? $first_parameter['card_expiracy_year'] : '';
			$this->card_cvv = $first_parameter["card_cvv"] ? $first_parameter['card_cvv'] : '';
			$this->installments = ($first_parameter['installments']) ? $first_parameter["installments"] : '';
			$this->postback_url = ($first_parameter['postback_url']) ? $first_parameter['postback_url'] : '';
		}
			$this->payment_method = ($first_parameter['payment_method']) ? $first_parameter['payment_method'] : 'credit_card';

		if($server_response) { 
			$this->updateFieldsFromResponse($server_response);
		}
	}

	public function charge() 
	{
		try {
			if(!$this->card_hash && $this->payment_method = 'credit_card') {
				$validation_error= $this->errorInTransaction();
				$this->card_hash = $this->generateCardHash();
				if($validation_error) {
					throw new Transaction_Exception($validation_error);
				}
			}

			if($this->status != 'local') {
				throw new Transaction_Exception("Transaction already charged!");
			}

			$request = new PagarMe_Request('/transactions', 'POST');
			$request->setParameters(array("amount" => $this->amount, "installments" => $this->installments, "card_hash" => ($this->payment_method == 'credit_card') ? $this->card_hash : null, 'postback_url' => $this->postback_url ));	
			$response = $request->run();
			$this->updateFieldsFromResponse($response);

			return $response;
		}
		catch(Transaction_Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function chargeback() 
	{
		try {
			if($this->status == 'chargebacked') {
				throw new Exception("Transaction already chargebacked!");
			}

			if($this->status != 'paid') {
				throw new Exception("Transaction needs to be paid to be chargebacked.");
			}

			if($this->payment_method != 'credit_card') {
				throw new Exception('Boletos can\'t be chargebacked');
			}

			$request = new PagarMe_Request('/transactions/'.$this->id . '/refund', 'POST');
			$response = $request->run();
			$this->updateFieldsFromResponse($response);

		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}

	}
}

?>
