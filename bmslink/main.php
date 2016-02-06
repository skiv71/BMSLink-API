<?php

class BMSLink {

	private static function loader() {

	    $files = scandir('bmslink/include');

	    foreach ($files as $idx=>$file) {

	    	if ($idx > 1) {

	    		require_once 'bmslink/include/' . $file;

	    	}

	    }

	}

	private static function route($req) {

		return BMSLink\Common::route($req);

	}

	private static function getClass($route) {

		$routes = BMSLink\Common::routes();

		foreach ($routes as $key=>$val) {

			if ($key == $route)

				return $val;

		}

	}

	private static function init() {

		self::loader();

		BMSlink\SQL::init();
		
	}

	public static function parse($req) {

		self::init();

		$route = self::route($req);

		$class = self::getClass($route);

		if (isset($class)) {

			$method = $req->method;

			$methods = get_class_methods($class);

			if (in_array($method, $methods)) {

				$class::$method($req);
	
			} else {

				print $method . ' not implemented';

			}

		} else {

			print 'invalid route!';

		}

	}

}