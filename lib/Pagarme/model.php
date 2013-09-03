<?php
abstract class PagarMe_Model extends Pagarme 
{	
	protected static $root_url;

	public static function getUrl() {
		$class = get_called_class();
		$search = preg_match("/PagarMe_(.*)/",$class, $matches);
		return '/'. strtolower($matches[1]) . 's';
	}

	public static function findById($id) 
	{
		$request = new PagarMe_Request(self::getUrl() . '/' . $id, 'GET');
		$response = $request->run();
		$class = get_called_class(); 
		return new $class(0, $response);
	}

	public static function all($page = 1, $count = 10) 
	{
		$request = new PagarMe_Request(self::getUrl(), 'GET');
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
