<?php

require_once 'flight/Flight.php';

require_once 'bmslink/main.php';

Flight::route('/*', function() {

	$req = Flight::request();

	BMSlink::parse($req);
	
});

Flight::start();

?>