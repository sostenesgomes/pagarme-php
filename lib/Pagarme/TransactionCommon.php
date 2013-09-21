<?php
class PagarMe_TransactionCommon extends PagarMe_Model 
{
	protected $id, $amount, $card_number, $card_holder_name, $card_expiracy_month, $card_expiracy_year, $card_cvv, $card_hash, $postback_url, $payment_method, $status, $date_created;
	protected $name, $document_number, $document_type, $email, $sex, $born_at, $customer; 
	protected $street, $city, $state, $neighborhood, $zipcode, $street_2, $street_number, $country;
	protected $phone_type, $ddi, $ddd, $number, $phone_id;
	protected $resfuse_reason, $antifraud_score, $boleto_url, $boleto_barcode;

	protected function generateCardHash() 
	{
		$request = new PagarMe_Request('/transactions/card_hash_key','GET');
		$response = $request->run();
		$key = openssl_get_publickey($response['public_key']);
		openssl_public_encrypt(http_build_query($this->cardDataParameters()), $encrypt, $key);
		return $response['id'].'_'.base64_encode($encrypt);
	}

	//TODO Validate address and phone info
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

			else {
				return null;
			}
		}
		if($this->amount <= 0) {
			return "Invalid amount.";
		}

		if(checkCustomerInformation()) {
			if(!$this->street || !$this->city || !$this->state || !$this->neighborhood || !$this->zipcode || !$this->street_number || !$this->ddd || !$this->number || !$this->name || !$this->document_type || !$this->document_number || !$this->email || !$this->sex || !$this->born_at ) {
				return "If you want to send customer information you need to send all of it.";
			}
		}

		return null;
	}

	protected function checkCustomerInformation() {
		if($this->street || $this->city || $this->state || $this->neighborhood || $this->zipcode || $this->street_2 || $this->country || $this->street_number || $this->phoneType || $this->ddi || $this->ddd || $this->number || $this->name || $this->document_number || $this->document_type || $this->email || $this->sex || $this->born_at || $this->phones) {
			return true;
		} else {
			return false;
		}

	}

	protected function mergeCustomerInformation($transactionInfo) {
		$transactionInfo['customer']['phone']['phone_type'] = $this->phone_type;
		$transactionInfo['customer']['phone']['ddi'] = ($this->ddi) ? $this->ddi : '55';
		$transactionInfo['customer']['phone']['ddd'] = $this->ddd;
		$transactionInfo['customer']['phone']['number'] = $this->number;
		$transactionInfo['customer']['phone']['type'] = $this->phone_type;
		$transactionInfo['customer']['address']['street_number'] = $this->street_number;
		$transactionInfo['customer']['address']['street'] = $this->street;
		$transactionInfo['customer']['address']['city'] = $this->city;
		$transactionInfo['customer']['address']['state'] = $this->state;
		$transactionInfo['customer']['address']['country'] = ($this->country) ? ($this->country) : "Brasil";
		$transactionInfo['customer']['address']['zipcode'] = $this->zipcode;
		$transactionInfo['customer']['address']['street_2'] = $this->street_2;
		$transactionInfo['customer']['address']['neighborhood'] = $this->neighborhood;
		$transactionInfo['customer']['document_number'] = $this->document_number;
		$transactionInfo['customer']['document_type'] = $this->document_type;
		$transactionInfo['customer']['email'] = $this->email;
		$transactionInfo['customer']['sex'] = $this->sex;
		$transactionInfo['customer']['born_at'] = $this->born_at;
		$transactionInfo['customer']['name'] = $this->name;
		return $transactionInfo;
	}

	protected function updateFieldsFromResponse($first_parameter)  
	{

		$this->amount = $first_parameter["amount"] ? $first_parameter['amount'] : '';
		$this->status = $first_parameter['status'] ? $first_parameter['status'] : 'local';
		$this->setCustomer($first_parameter['customer']);
		if(!$first_parameter['card_hash']) { 
			$this->card_number = ($first_parameter["card_number"]) ? $first_parameter['card_number']  : '';
			$this->card_holder_name = ($first_parameter["card_holder_name"]) ? $first_parameter['card_holder_name'] : '';
			$this->card_expiracy_month = ($first_parameter["card_expiracy_month"]) ? $first_parameter['card_expiracy_month'] : '';
			$this->card_expiracy_year = ($first_parameter["card_expiracy_year"]) ? $first_parameter['card_expiracy_year'] : '';
			$this->card_cvv = $first_parameter["card_cvv"] ? $first_parameter['card_cvv'] : '';
			$this->installments = ($first_parameter['installments']) ? $first_parameter["installments"] : '';
			$this->postback_url = ($first_parameter['postback_url']) ? $first_parameter['postback_url'] : '';
		} elseif($first_parameter['card_hash']) {
			$this->card_hash = $first_parameter['card_hash'];
		}

		$this->payment_method = ($first_parameter['payment_method']) ? $first_parameter['payment_method'] : 'credit_card';
		$this->street = ($first_parameter['street']) ? $first_parameter['street'] : 0;
		$this->city = ($first_parameter['city']) ? $first_parameter['city'] : '';
		$this->state = ($first_parameter['state']) ? $first_parameter['state'] : '';
		$this->state = ($first_parameter['state']) ? $first_parameter['state'] : '';
		$this->neighborhood = ($first_parameter['neighborhood']) ? $first_parameter['neighborhood'] : '';
		$this->zipcode = ($first_parameter['zipcode']) ? $first_parameter['zipcode'] : '';
		$this->street_2 = ($first_parameter['street_2']) ? $first_parameter['street_2'] : '';
		$this->street_number = ($first_parameter['street_number']) ? $first_parameter['street_number'] : '';
		$this->country = ($first_parameter['country']) ? $first_parameter['country'] : '';
		$this->phone_type = ($first_parameter['phone_type']) ? $first_parameter['phone_type'] : '';
		$this->ddi = ($first_parameter['ddi']) ? $first_parameter['ddi'] : '';
		$this->ddd = ($first_parameter['ddd']) ? $first_parameter['ddd'] : '';
		$this->number = ($first_parameter['number']) ? $first_parameter['number'] : '';
		$this->id = ($first_parameter['id']) ? $first_parameter['id'] : '';
		$this->name = ($first_parameter['name']) ? $first_parameter['name'] : '';
		$this->document_type = ($first_parameter['document_type']) ? $first_parameter['document_type'] : '';
		$this->document_number = ($first_parameter['document_number']) ? $first_parameter['document_number'] : '';
		$this->email = ($first_parameter['email']) ? $first_parameter['email'] : '';
		$this->born_at = ($first_parameter['born_at']) ? $first_parameter['born_at'] : '';
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

	//Address Info
	public function setStreet($street) {$this->street = $street;}
	public function getStreet() { return $this->street;}

	public function setCity($city) {$this->city = $city;}
	public function getCity() { return $this->city;}

	public function setState($state) {$this->state = $state;}
	public function getState() { return $this->state;}

	public function setNeighborhood($neighborhood) {$this->neighborhood = $neighborhood;}
	public function getNeighborhood() { return $this->neighborhood;}

	public function setZipcode($zipcode) {$this->zipcode = $zipcode;}
	public function getZipcode() { return $this->zipcode;}

	public function getAddressId() { return $this->address_id;}

	public function setStreet2($street2) {$this->street2 = $street2;}
	public function getStreet2() { return $this->street2;}

	public function setStreetNumber($street_number) {$this->street_number = $street_number;}
	public function getStreetNumber() { return $this->street_number;}

	public function setCountry($country) {$this->country = $country;}
	public function getCountry() { return $this->country;}

	// Phone Info

	public function setPhoneType($phone_type) {$this->phone_type = $phone_type;}
	public function getPhoneType() {return $this->phone_type;}

	public function setDDI($ddi) {$this->ddi = $ddi;}
	public function getDDI() {return $this->ddi;}

	public function setDDD($ddd) {$this->ddd = $ddd;}
	public function getDDD() {return $this->ddd;}

	public function setNumber($number) {$this->number = $number;}
	public function getNumber() {return $this->number;}

	public function getPhoneId() {return $this->phone_id;}


	//Customer info

	public function getName() { return $this->name;}
	public function setName($name) { $this->name = $name; }

	public function getDocumentNumber() { return $this->document_number;}
	public function setDocumentNumber($document_number) { $this->document_number = $document_number; }

	public function getDocumentType() { return $this->document_type;}
	public function setDocumentType($document_type) { $this->document_type = $document_type; }

	public function getEmail() { return $this->email;}
	public function setEmail($email) { $this->email = $email; }

	public function getSex() { return $this->sex;}
	public function setSex($sex) { $this->sex = $sex; }

	public function setCustomer($customer) {
		if($customer) { 
			$this->customer = new PagarMe_Customer($customer);
		}
	}

	public function getCustomer() {
		return $this->customer;
	}


	public function getBornAt() { return $this->born_at;}
	public function setBornAt($born_at) { $this->born_at = $born_at; }

	public function getRefuseReason() { return $this->refuse_reason;}

	public function getAntifraudeScore() { return $this->antifraud_score;}

	public function getBoletoUrl() { return $this->boleto_url;}

	public function getBoletoBarcode() { return $this->boleto_barcode;}
} 

?>
