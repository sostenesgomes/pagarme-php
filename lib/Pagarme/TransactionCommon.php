<?php
class PagarMe_TransactionCommon extends PagarMe_Model 
{
	protected $id, $amount, $card_number, $card_holder_name, $card_expiracy_month, $card_expiracy_year, $card_cvv, $card_hash, $postback_url, $payment_method, $status, $date_created;

	protected function generateCardHash() 
	{
		$request = new PagarMe_Request('/transactions/card_hash_key','GET');
		$response = $request->run();
		$key = openssl_get_publickey($response['public_key']);
		openssl_public_encrypt(http_build_query($this->cardDataParameters()), $encrypt, $key);
		return $response['id'].'_'.base64_encode($encrypt);
	}

	protected function errorInTransaction() 
	{
		if($this->payment_method == 'credit_card') { 
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
	}

	protected function updateFieldsFromResponse($r)  
	{
		$this->status = $r["status"];
		$this->date_created = $r["date_created"];
		$this->amount = $r["amount"];
		$this->card_holder_name = $r["card_holder_name"];
		$this->installments = (!$r["installments"]) ? 1 : $r["installments"];
		$this->id = $r["id"];
		$this->payment_method = ($r['payment_method']) ? $r['payment_method'] : 'credit_card';
	}

	protected function cardDataParameters() 
	{
		return array(
			"card_number" => $this->card_number,
			"card_holder_name" => $this->card_holder_name,
			"card_expiracy_date" => $this->card_expiracy_month . $this->card_expiracy_year,
			"card_cvv" => $this->card_cvv
		);
	}

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
		function setPaymentMethod($payment_method) {$this->payment_method = $payment_method;}
		function getPaymentMethod(){return $this->payment_method;}
		function setDateCreated($date_created) { $this->date_created = $date_created;}
		function getDateCreated() { return $this->date_created;}
		function getId() { return $this->id; }
		function setId($id) {$this->id = $id;}
} 

?>
