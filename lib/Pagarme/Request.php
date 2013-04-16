<?php
require 'RestClient.php';
require 'Pagarme.php';
require 'Exception.php';
class PagarMe_Request extends PagarMe {

	private $path;
	private $method;
	private $parameters;
	private $headers;
	private $live;

	public function __construct($path, $method, $live = PagarMe::live) {
		$this->method = $method;
		$this->path = $path;
		$this->live = $live;	
		$this->parameters = Array();
	}
	public function run() {
		try {

			if(!self::api_key) {
				throw new Exception("You need to configure a API key before performing requests.");
			}

			array_merge($this->parameters, array( "api_key" => PagarMe::api_key, "live" => PagarMe::live));

			try {
				$client = new RestClient(array("method" => $this->method, "url" => $this->full_api_url($this->path), "headers" => $this->headers, "parameters" => $this->parameters ));	
				$response = $client->run();
				if($response["code"] == 200) {
					$decode = json_decode($response["body"]);
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
}
?>
