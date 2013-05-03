<?php

abstract class PagarMe {

	public static $api_key; 
	const live = 1;
	const endpoint = "https://localhost:4000";
	const api_version = '1';


	public function full_api_url($path) {
		return self::endpoint . '/' . self::api_version . $path;
	}


	public static function setApiKey($api_key) {
		self::$api_key = $api_key; 
	}

	public static function getApiKey() {
		return self::$api_key;
	}
}


?>
