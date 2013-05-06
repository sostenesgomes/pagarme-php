<?php

class RestClient_Exception extends PagarMe_Exception {

}


class RestClient {

	private $http_client;
	private $method;
	private $url;
	private $headers = Array();
	private $parameters =  Array();
	private $curl;

	public function __construct($params = array()) {
		try {
			$this->curl = curl_init();


			$this->headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
);

			if(!$params["url"]) {
				throw new Exception("You must set the URL to make a request.");
			} else {
				$this->url = $params["url"];
			}

			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->curl, CURLOPT_SSLVERSION, 3);
			curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
			

			if($params["parameters"]) {
				$this->parameters = array_merge($this->parameters, $params["parameters"]);
			}

			if($params["method"]) {
				$this->method = $params["method"];
			}

			 if ($this->method){
				switch($this->method) {
				case 'post':
				case 'POST':
					curl_setopt($this->curl, CURLOPT_POST, true);
					curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);	
					break;
				case 'get':
				case 'GET':
					$this->url .= '?'.http_build_query($this->parameters);
					break;
				case 'put':
				case 'PUT':
					$this->method = HTTP_METH_PUT;
					curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);
					break;
				case 'delete':
				case 'DELETE':
					$this->method = HTTP_METH_DELETE;
					curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
					break;

				}
			}


	
				curl_setopt($this->curl, CURLOPT_URL, $this->url);	


			if($params["headers"]) {
				$this->headers = array_merge($this->headers, $params["headers"]);
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
			}


		} catch(HttpException $e) {
			throw new Exception($e->message);
		}

	}


	public function run() {
		try {
		$response = curl_exec($this->curl);
		$error = curl_error($this->curl);
		if($error) {
			throw new Exception("error: ".$error);
		}
		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		curl_close($this->curl);
		return array("code" => $code, "body" => $response);
		} catch(Exception $e) {
			throw new PagarMe_Exception($e->getMessage());

		}	
	}

}


?>
