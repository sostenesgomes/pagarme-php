<?php

class PagarMe_Customer extends PagarMe_Model {
	private $id, $name, $document_number, $document_type, $email, $addresses, $sex, $born_at, $phones;


	public function __construct($customer = 0) {
		$this->updateFieldsFromResponse($customer);
	}

	//TODO try to assume document_type
	protected function updateFieldsFromResponse($serverResponse) {
			$this->id = ($serverResponse['id']) ?  $serverResponse['id'] : 0;
			$this->name = ($serverResponse['name']) ? $serverResponse['name'] : 0;
			$this->document_number = ($serverResponse['document_number']) ? $serverResponse['document_number'] : 0;
			$this->document_type = ($serverResponse['document_type']) ? $serverResponse['document_type'] : 0;
			$this->email = ($serverResponse['email']) ? $serverResponse['email'] : 0;
			$this->setAddresses($serverResponse['addresses']);
			$this->sex = ($serverResponse['sex']) ? $serverResponse['sex'] : 0;
			$this->born_at = ($serverResponse['born_at']) ? $serverResponse['born_at'] : 0;
			$this->setPhones($serverResponse['phones']);
	}


	public function getName() { return $this->name;}
	public function setName($name) { $this->name = $name; }

	public function getDocumentNumber() { return $this->document_number;}
	public function setDocumentNumber($document_number) { $this->document_number = $document_number; }

	public function getDocumentType() { return $this->document_type;}
	public function setDocumentType($document_type) { $this->document_type = $document_type; }

	public function getEmail() { return $this->email;}
	public function setEmail($email) { $this->email = $email; }

	public function getAddresses() { return $this->addresses;}
	public function setAddresses($addresses) { 
		if($addresses) {
			foreach($addresses as $address) {
				$this->addresses[] = new PagarMe_Address($address);	
			}
		}
	}

	public function getPhones() { return $this->phones;}
	public function setPhones($phones) { 
		if($phones) {
			foreach($phones as $phone) {
				$this->phones[] = new PagarMe_Phone($phone);	
			}
		}
	}

	public function getSex() { return $this->sex;}
	public function setSex($sex) { $this->sex = $sex; }

	public function getId() { return $this->id;}

	public function getBornAt() { return $this->born_at;}
	public function setBornAt($born_at) { $this->born_at = $born_at; }
	

}

?>
