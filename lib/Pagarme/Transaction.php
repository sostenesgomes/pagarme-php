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
			$this->card_expiracy_mont = $first_parameter["card_expiracy_month"];
			$this->card_expiracy_year = $first_parameter["card_expiracy_year"];
			$this->card_cvv = $first_parameter["card_cvv"];
			$this->installments = $first_parameter["installments"];
			if($first_parameter["live"]) {
				$this->live = $first_parameter["live"];
			}
		}

		$this->update_fields_from_response($server_response);
	}
	

	public function find_by_id($id) {
	
		$request = new PagarMe_Request('/transactions/'.$id, 'GET');
		$response = $request->run();
		return new PagarMe_Transaction(0, $response);
	}



	public function all($page = 1, $count = 10) {
		$request = new PagarMe_Request('/transactions','GET');
		$request->setParameters(array("page" => $page, "count" => $count));
		$response = $request->run();
		$return_array = Array();
		foreach($reponse as $r) {
			$return_array[] = new PagarMe_Transaction(0, $r);
		}

		return $return_array;
	}

	public function charge() {

		if(!$this->card_hash) {
			$validation_error = $this->error_in_transaction();
			$this->generate_card_hash();
		}

		$request = new PagarMe_Request('/transactions', 'POST');
		$request->setParameters(array("amount" => $this->amount, "installments" => $this->installments, "card_hash" => $this->card_hash ));	
		$response = $request->run();
		$this->update_fields_from_response($response);
	}


	public function chargeback() {
		try {
			if($this->status == $this->statuses_codes["chargebacked"]) {
				throw new Exception("Transaction already chargebacked!");
			}

			if($this->status != $this->statuses_codes["approved"]) {
				throw new Exception("Transaction needs to be approved to be chargebacked.");
			}

			$request = new PagarMe_Request('/transactions/'.$this->id.'/chargeback/', 'POST', $this->live);
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

	
	private function generate_card_hash() {
	
		$request = new PagarMe_Request('/transactions/card_hash_key', 'GET', $this->live);
		$response = $request->run();
		$key = openssl_get_publickey($response['public_key']);
		$encrypt = openssl_public_encrypt(base64_encode(http_build_query($this->card_data_parameters())));
		return $response['id'].'_'.$encrypt;

	}



	private function update_fields_from_response($r)  {
		$this->status = $this->statuses_codes[$r["status"]];
		$this->date_created = $r["date_created"];
		$this->amount = $r["amount"];
		$this->live = $r["live"];
		$this->card_holder_name = $r["card_holder_name"];
		$this->installments = (i$r["installments"]) ? 1 : $r["installments"];
		$this->id = $r["id"];
	}


}

?>
