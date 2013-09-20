<?php
class PagarMe_Phone extends PagarMe_Model {
	private $type, $ddi, $ddd, $number, $id;

	public function __construct($phone) {
			$this->updateFieldsFromResponse($phone);
	}

	public function updateFieldsFromResponse($serverResponse) {
			$this->type = ($firstParameter['type']) ? $firstParameter['type'] : 0; 
			$this->ddi = ($firstParameter['ddi']) ? $firstParameter['ddi'] : '55';
			$this->ddd = ($firstParameter['ddd']) ? $firstParameter['ddd'] : 0;
			$this->number = ($firstParameter['number']) ? $firstParameter['number'] : 0;
			$this->id = ($firstParameter['id'])	? $firstParameter['id'] : 0;
	}

	public function setType($type) {$this->type = $type;}
	public function getType() {return $this->type;}

	public function setDDI($ddi) {$this->ddi = $ddi;}
	public function getDDI() {return $this->ddi;}

	public function setDDD($ddd) {$this->ddd = $ddd;}
	public function getDDD() {return $this->ddd;}

	public function setNumber($number) {$this->number = $number;}
	public function getNumber() {return $this->number;}

	public function getId() {return $this->id;}
	
}

?>
