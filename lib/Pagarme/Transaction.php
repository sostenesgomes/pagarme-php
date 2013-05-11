<?php


class PagarMe_Transaction extends PagarMe {

	private $amount, $card_number, $card_holder_name, $card_expiracy_month, $card_expiracy_year, $card_cvv, $live, $card_hash, $installments;

	private $statuses_codes;

	private $date_created;

	private $status;

	private $id;

	public function __construct($first_parameter = 0, $server_response = 0) {

		$this->statuses_codes = array("local" => 0, "approved" => 1, "processing" => 2, "refused" => 3, "chargebacked" => 4 );
		$this->status = $this->statuses_codes["local"];

		$this->live = parent::live;

		$this->installments = 1;

		$this->amount = $this->card_number = $this->card_expicary_month = $this->card_expiracy_year = $this->card_cvv = "";


		if(gettype($first_parameter) == "string") {

			$this->card_hash = $first_parameter;
		} elseif(gettype($first_parameter) == "array") {

			$this->amount = $first_parameter["amount"];
			$this->card_number = $first_parameter["card_number"];
			$this->card_holder_name = $first_parameter["card_holder_name"];
			$this->card_expiracy_month = $first_parameter["card_expiracy_month"];
			$this->card_expiracy_year = $first_parameter["card_expiracy_year"];
			$this->card_cvv = $first_parameter["card_cvv"];
			$this->installments = $first_parameter["installments"];
			if($first_parameter["live"]) {
				$this->live = $first_parameter["live"];
			}
		}

		if($server_response) { 
			$this->update_fields_from_response($server_response);
		}
	}


	public static function find_by_id($id) {

		$request = new PagarMe_Request('/transactions/'.$id, 'GET');
		$response = $request->run();
		return new PagarMe_Transaction(0, $response);
	}



	public static function all($page = 1, $count = 10) {
		$request = new PagarMe_Request('/transactions','GET');
		$request->setParameters(array("page" => $page, "count" => $count));
		$response = $request->run();
		$return_array = Array();
		foreach($response as $r) {
			$return_array[] = new PagarMe_Transaction(0, $r);
		}

		return $return_array;
	}

	public function charge() {

		try {
			if(!$this->card_hash) {
				$validation_error= $this->error_in_transaction();
				$this->card_hash = $this->generate_card_hash();
				if($validation_error) {
					throw new Transaction_Exception($validation_error);
				}
			}

			$request = new PagarMe_Request('/transactions', 'POST', $this->live);
			$request->setParameters(array("amount" => $this->amount, "installments" => $this->installments, "card_hash" => $this->card_hash ));	
			$response = $request->run();
			$this->update_fields_from_response($response);

			return $response;
		}
		catch(Transaction_Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}


	public function chargeback() {
		try {
			if($this->status == $this->statuses_codes["chargebacked"]) {
				throw new Exception("Transaction already chargebacked!");
			}

			if($this->status != $this->statuses_codes["approved"]) {
				throw new Exception("Transaction needs to be approved to be chargebacked.");
			}

			$request = new PagarMe_Request('/transactions/'.$this->id, 'DELETE', $this->live);
			$response = $request->run();
			$this->update_fields_from_response($response);

		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}

	}


	private function card_data_parameters() {
		return array(
			"card_number" => $this->card_number,
			"card_holder_name" => $this->card_holder_name,
			"card_expiracy_date" => $this->card_expiracy_month . $this->card_expiracy_year,
			"card_cvv" => $this->card_cvv
		);


	}


	private function error_in_transaction() {

		if(strlen($this->card_number) < 16 || strlen($this->card_number) > 20) {
			return "Invalid card number";
		}

		else if(strlen($this->card_holder_name) == 0) {
			return "Invalid card holder name.";
		}

		else if($this->card_expiracy_month <= 0 || $this->card_expiracy_month > 12) {
			return "Invalid expiracy month.";
		}

		else if($this->card_expiracy_year <= 0) {
			return "Invalid expiracy year";
		}
		else if($this->card_expiracy_year < substr(date('Y'),-2)) {
			return "Card already expired";
		}

		else if(strlen($this->card_cvv) < 3  || strlen($this->card_cvv) > 4) {
			return "Invalid card security code";
		}

		else if($this->amount <= 0) {
			return "Invalid amount.";
		}
		else {
			return null;
		}
	}


	private function generate_card_hash() {

		$request = new PagarMe_Request('/transactions/card_hash_key','GET', $this->live);
		$response = $request->run();
		$key = openssl_get_publickey($response['public_key']);
		openssl_public_encrypt(http_build_query($this->card_data_parameters()), $encrypt, $key);
		return $response['id'].'_'.base64_encode($encrypt);

	}



	private function update_fields_from_response($r)  {
		$this->status = $this->statuses_codes[$r["status"]];
		$this->date_created = $r["date_created"];
		$this->amount = $r["amount"];
		$this->live = $r["live"];
		$this->card_holder_name = $r["card_holder_name"];
		$this->installments = (!$r["installments"]) ? 1 : $r["installments"];
		$this->id = $r["id"];
	}



	// Setters and getters


	function setAmount($amount) { $this->amount = $amount; }
		function getAmount() { return $this->amount; }
		function setCardNumber($card_number) { $this->card_number = $card_number; }
		function getCardNumber() { return $this->card_number; }
		function setCardHolderName($card_holder_name) { $this->card_holder_name = $card_holder_name; }
		function getCardHolderName() { return $this->card_holder_name; }
		function setCardExpiracyMonth($card_expiracy_month) { $this->card_expiracy_month = $card_expiracy_month; }
		function getCardExpiracyMonth() { return $this->card_expiracy_month; }
		function setCardExpiracyYear($card_expiracy_year) { $this->card_expiracy_year = $card_expiracy_year; }
		function getCardExpiracyYear() { return $this->card_expiracy_year; }
		function setCardCvv($card_cvv) { $this->card_cvv = $card_cvv; }
		function getCardCvv() { return $this->card_cvv; }
		function setLive($live) { $this->live = $live; }
		function getLive() { return $this->live; }
		function setCardHash($card_hash) { $this->card_hash = $card_hash; }
		function getCardHash() { return $this->card_hash; }
		function setInstallments($installments) { $this->installments = $installments; }
		function getInstallments() { return $this->installments; }
		function getStatus() { return $this->status; }
		function setStatus($status) { $this->status = $status;}
}

?>
