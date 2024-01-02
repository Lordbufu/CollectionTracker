<?php
	/* Standaard entry point, voor dit project is niet echt iets complex nodig */
	require '../vendor/autoload.php';
	require '../core/bootstrap.php';

	use App\Core\{Router, Request};

	// Laad de routes in de app
	Router::load('../app/routes.php')->direct(Request::uri(), Request::method());
?>