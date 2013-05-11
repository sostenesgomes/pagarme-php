<?php
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
				throw new PagarMe_Exception("You need to configure API key before performing requests.");
			}


			$this->parameters = array_merge($this->parameters, array( "api_key" => parent::getApiKey(),  "live" => PagarMe::live));
			try {

				$client = new RestClient(array("method" => $this->method, "url" => $this->full_api_url($this->path), "headers" => $this->headers, "parameters" => $this->parameters ));	
				$response = $client->run();
				$decode = json_decode($response["body"], true);
				if(!$decode) {
					throw new PagarMe_Exception("failed to decode json from response.");
				}
				else {
					if($response["code"] == 200) {
						return $decode;

					} else {
						throw new PagarMe_ApiError($decode["error"], $decode["url"], $decode["method"],  $response["code"]);
					}
				}
			} catch(RestClient_Exception $e) {
				throw new PagarMe_Exception($e->getMessage());

			}		
		} catch(Exception $e) {

			throw $e;
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
