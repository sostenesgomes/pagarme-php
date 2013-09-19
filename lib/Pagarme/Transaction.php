<?php
class PagarMe_Transaction extends PagarMe_TransactionCommon {

	protected $installments;

	private $statuses_codes;

	public function __construct($first_parameter = 0) 
	{
		$this->status = 'local';

		$this->installments = 1;

		$this->amount = $this->card_number = $this->card_expicary_month = $this->card_expiracy_year = $this->card_cvv = "";

		$this->postback_url = null;

		$this->payment_method = 'credit_card';

		$this->updateFieldsFromResponse($first_parameter);
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

			$transactionInfo = array("amount" => $this->amount, "installments" => $this->installments, "card_hash" => ($this->payment_method == 'credit_card') ? $this->card_hash : null, 'postback_url' => $this->postback_url );

			if($this->checkCustomerInformation()) {
				$transactionInfo = $this->mergeCustomerInformation($transactionInfo);
			}

			$request = new PagarMe_Request(self::getUrl(), 'POST');
			$request->setParameters($transactionInfo);	

			$response = $request->run();
			$this->updateFieldsFromResponse($response);

			return $response;
		}
		catch(Transaction_Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}


	public function refund() 
	{
		try {
			if($this->status == 'refunded') {
				throw new Exception("Transaction already refunded!");
			}

			if($this->status != 'paid') {
				throw new Exception("Transaction needs to be paid to be refunded.");
			}

			if($this->payment_method != 'credit_card') {
				throw new Exception('Boletos can\'t be refunded');
			}

			$request = new PagarMe_Request(self::getUrl().'/'.$this->id . '/refund', 'POST');
			$response = $request->run();
			$this->updateFieldsFromResponse($response);

		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}

	}
}

?>
