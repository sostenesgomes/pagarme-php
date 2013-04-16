<?php

class PagarMe {

	const api_key = "db6484a69325a556f210d1212aaec14d";
	
	const live = 1;
	const endpoint = 'https://localhost:3000';
	const api_version = '1';


	public function full_api_url($path) {
		return self::endpoint . '/' . self::api_version . $path;
	}
}


?>
