<?php
class PagarMe_Address extends PagarMe_Model {
	private $street, $city, $state, $neighborhood, $zipcode, $street_2, $id, $street_number, $country;

	public function __construct($firstParameter = 0, $serverResponse = 0) {
		if($firstParameter) {
			$this->street = ($firstParameter['street']) ? $firstParameter['street'] : 0;
			$this->city = ($firstParameter['city']) ? $firstParameter['city'] : 0;
			$this->state = ($firstParameter['state']) ? $firstParameter['state'] : 0;
			$this->neighborhood = ($firstParameter['neighborhood']) ? $firstParameter['neighborhood'] : 0;
			$this->zipcode = ($firstParameter['zipcode']) ? $firstParameter['zipcode'] : 0;
			$this->street_2 = ($firstParameter['street_2']) ? $firstParameter['street_2'] : 0;
			$this->street_number = ($firstParameter['street_number']) ? $firstParameter['street_number'] : 0;
			$this->country = ($firstParameter['country']) ? $firstParameter['country'] : 'Brasil';
		} else {
			$this->updateFieldsFromResponse($serverResponse);
		}
	}

	public function updateFieldsFromResponse($serverResponse) {
			$this->id = ($firstParameter['id']) ? $firstParameter['id'] : 0;
			$this->street = ($firstParameter['street']) ? $firstParameter['street'] : 0;
			$this->city = ($firstParameter['city']) ? $firstParameter['city'] : 0;
			$this->state = ($firstParameter['state']) ? $firstParameter['state'] : 0;
			$this->neighborhood = ($firstParameter['neighborhood']) ? $firstParameter['neighborhood'] : 0;
			$this->zipcode = ($firstParameter['zipcode']) ? $firstParameter['zipcode'] : 0;
			$this->street_2 = ($firstParameter['street_2']) ? $firstParameter['street_2'] : 0;
			$this->street_number = ($firstParameter['street_number']) ? $firstParameter['street_number'] : 0;
	}

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

	public function getId() { return $this->id;}

	public function setStreet2($street2) {$this->street2 = $street2;}
	public function getStreet2() { return $this->street2;}

	public function setStreetNumber($street_number) {$this->street_number = $street_number;}
	public function getStreetNumber() { return $this->street_number;}

	public function setCountry($country) {$this->country = $country;}
	public function getCountry() { return $this->country;}
}
?>
