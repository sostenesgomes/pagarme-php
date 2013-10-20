<?php

class PagarMe_Plan extends PagarMe_Model 
{
	private $id, $amount, $days, $name, $trial_days;

	public function __construct($plan = 0) {
		$this->updateFieldsFromResponse($plan);
	}

	public function create() 
	{
		try {
			$this->validate();
			$request = new PagarMe_Request(self::getUrl(), 'POST');
			$request->setParameters(array('amount' => $this->amount, 'days' => $this->days, 'name' => $this->name, 'trial_days' => $this->trial_days));
			$response = $request->run();
			$this->updateFieldsFromResponse($response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function update() 
	{
		try {
			$this->validate();
			if(!$this->id) {
				throw new Exception("O plano precisa estar criado para ser editado.");
			}
			$request = new PagarMe_Request(self::getUrl(). '/' . $this->id, 'PUT');
			$request->setParameters(array('name' => $this->name, 'trial_days' => $this->trial_days));
			$response = $request->run();
			$this->updateFieldsFromResponse($response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	protected function validate() 
	{
		if($this->amount <= 0) {
			throw new Exception("Amount invalido!");
		} else if($this->days <= 0) {
			throw new Exception("Days inválido!");
		} else if(strlen($this->name) <= 0) {
			throw new Exception("Name inválido!");
		} else if($this->trial_days < 0) {
			throw new Exception("Trial days invalido!");
		} else {
			return true;
		}
	}

	protected function updateFieldsFromResponse($firstParameter) 
	{
		$this->amount = ($firstParameter['amount']) ? $firstParameter['amount'] : 0;
		$this->amount = trim($this->amount);
		$this->amount = str_replace(',', "", $this->amount);
		$this->amount = str_replace('.', "", $this->amount);
		$this->amount = str_replace('R$', "", $this->amount);

		$this->days = ($firstParameter['days']) ? $firstParameter['days'] : 0;
		$this->name = ($firstParameter['name']) ? $firstParameter['name'] : 0;
		$this->trial_days = ($firstParameter['trial_days']) ? $firstParameter['trial_days'] : 0;
		$this->id = ($firstParameter['id']) ? $firstParameter['id'] : 0;
	}

	public function getAmount() 
	{ 
		return $this->amount;
	}

	public function setAmount($amount) 
	{
		try {
			if($this->id) {
				throw new Exception("Amount não pode ser editado");
			} else {
				$this->amount = $amount;
			}

		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function getDays() 
	{ 
		return $this->days;
	}

	public function setDays($days) 
	{
		try {
			if($this->id) {
				throw new Exception("Days não pode ser editado");
			} else {
				$this->days = $days;
			}

		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}

	public function getName() 
	{ 
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getTrialDays() 
	{ 
		return $this->trial_days;
	}

	public function setTrialDays($trial_days) {
		$this->trial_days = $trial_days;
	}

	public function getId() 
	{ 
		return $this->id;
	}

	public function setId($id) 
	{
		try {
			if($this->id) {
				throw new Exception("ID não pode ser editado");
			} else {
				$this->id = $id;
			}

		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());
		}
	}


}


?>
