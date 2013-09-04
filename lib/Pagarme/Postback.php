<?php

class PagarMe_Postback extends PagarMe_Model {

	protected $old_status, $desired_status, $current_status, $id;

	public function __construct() {
		if(isset($_GET)) {
			$this->current_status = $_GET['current_status'];
			$this->desired_status = $_GET['desired_status'];
			$this->id = $_GET['id'];
			$this->old_status = $_GET['old_status'];
		}
	}

	public function worked() {
		return $this->current_status == $this->desired_status;
	}

	public function getOldStatus() {return $this->old_status;}
	public function getDesiredStatus() {return $this->desired_status;}
	public function getCurrentStatus() {return $this->current_status;}
	public function getId() {return $this->id;}
}

?>
