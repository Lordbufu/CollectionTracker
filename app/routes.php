<?php
	/* Get routes for the PagesController */
	$router->get('',				'PagesController@landing');		// Main landingpage.
	$router->get('beheer',			'PagesController@beheer');		// Main admin page.
	$router->get('gebruik',			'PagesController@gebruik');		// Main user page.

	/* Post routes for the LogicController */
	$router->get('createDB',		'LogicController@dbCreation');	// Database creation route.
	$router->post('register',		'LogicController@register');	// Account register route.
	$router->post('login',			'LogicController@login');		// Account login route.
	$router->post('logout',			'LogicController@logout');		// Shared account logout route.
	$router->post('valUsr',			'LogicController@valUsr');		// Shared validate user route.
	$router->post('beheer',			'LogicController@beheer');		// Main admin page.
	$router->post('serieM',			'LogicController@serieM');		// Admin add serie route.
	$router->post('serieBek',		'LogicController@serieBek');	// Admin view serie route.
	$router->post('serieBew',		'LogicController@serieBew');	// Admin serie edit route.
	$router->post('serieVerw',		'LogicController@serieVerw');	// Admin serie remove route.
	$router->post('albumT',			'LogicController@albumT');		// Admin album add route.
	$router->post('albumBew',		'LogicController@albumBew');	// Admin album edit route.
	$router->post('albumV',			'LogicController@albumV');		// Admin album remove route.
	$router->post('aReset',			'LogicController@adminReset');	// Admin password reset route.
	$router->post('gebruik',		'LogicController@gebruik');		// Main user page.
	$router->post('albSta',			'LogicController@albSta');		// Album status change route.

	/* TEST routes for scanning barcodes and searching isbn numbers */
	$router->post('scan', 			'LogicController@scan');		// Admin scan trigger.
	$router->post('isbn', 			'LogicController@isbn');		// Admin scan/search result evaluation.
	$router->post('userScan',		'LogicController@userScan');	// User scan trigger.
	$router->post('userIsbn',		'LogicController@userIsbn');	// User scan result evaluation.
?>