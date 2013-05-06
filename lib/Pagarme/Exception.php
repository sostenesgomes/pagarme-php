<?php

class PagarMe_Exception extends Exception {
	protected $url, $method, $return_code;


	public function __construct($message = null, $url = null, $method = null, $code = null) {
		$this->url = $url;
		$this->method = $method;
		$this->return_code = $code;

		parent::__construct($message);
	}

	public function getErrorMessage() {
		return $this->errorMessage;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getMethod() {
		return $this->method;
	}

	public function getReturnCode() {
		return $this->return_code;
	}


}


?>
