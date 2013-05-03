<?php
require 'RestClient.php';
require 'Pagarme.php';
require 'Exception.php';
class PagarMe_Request extends PagarMe {

	private $path;
	private $method;
	private $parameters = Array();
	private $headers;
	private $live;

	public function __construct($path, $method, $live = PagarMe::live) {
		$this->method = $method;
		$this->path = $path;
		$this->live = $live;	
	}
	public function run() {
		try {

			if(!parent::getApiKey()) {
				throw new Exception("You need to configure API key before performing requests.");
			}


			$this->parameters = array_merge($this->parameters, array( "api_key" => parent::getApiKey(),  "live" => PagarMe::live));
			
			try {

				$client = new RestClient(array("method" => $this->method, "url" => $this->full_api_url($this->path), "headers" => $this->headers, "parameters" => $this->parameters ));	
				$response = $client->run();
				if($response["code"] == 200) {
					$decode = json_decode($response["body"], true);
					if(!$decode) {
						throw new Exception("Couldn't decode json from response");
					} else {
						return $decode;
					}
				} else {

					throw new Exception("Code error code: " . $response["code"]);
				}

			} catch(RestClient_Exception $e) {
				throw new Exception($e->getMessage());

			}		
		} catch(Exception $e) {

			throw new PagarMe_Exception($e->getMessage());
		}

	}


	public function setParameters($parameters) {
		$this->parameters = $parameters;
	}

	public function getParameters() {
		return $this->parameters;
	}
}
?>
