<?php
abstract class PagarMe_Model extends Pagarme 
{	
	protected static $root_url;

	public static function findById($id) 
	{
		$request = new PagarMe_Request(self::$root_url . '/' . $id, 'GET');
		$response = $request->run();
		$class = get_called_class(); 
		return new $class(0, $response);
	}

	public static function all($page = 1, $count = 10) 
	{
		$request = new PagarMe_Request(self::$root_url, 'GET');
		$request->setParameters(array("page" => $page, "count" => $count));
		$response = $request->run();
		$return_array = Array();
		$class = get_called_class(); 
		foreach($response as $r) {
			$return_array[] = new $class(0, $r);
		}

		return $return_array;
	}

}
?>
