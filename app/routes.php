<?php
	/* Get routes for the PagesController */
	$router->get('',				'PagesController@landing');
	$router->get('beheer',			'PagesController@beheer');
	$router->get('gebruik',			'PagesController@gebruik');

	/* Post routes for the LogicController */
	// Account related routes
	$router->post('register',		'LogicController@register');
	$router->post('login',			'LogicController@login');
	$router->post('logout',			'LogicController@logout');

	// Admin related routes
	$router->post('beheer',			'LogicController@beheer');
	$router->post('albumT',			'LogicController@albumT');
	$router->post('albumBew',		'LogicController@albumBew');
	$router->post('albumV',			'LogicController@albumV');
	$router->post('serieM',			'LogicController@serieM');
	$router->post('serieBek',		'LogicController@serieBek');
	$router->post('serieBew',		'LogicController@serieBew');
	$router->post('serieVerw',		'LogicController@serieVerw');
	$router->post('aReset',			'LogicController@adminReset');

	// User related routes
	$router->post('gebruik',		'LogicController@gebruik');
	$router->post('albSta',			'LogicController@albSta');

	// Shared routes
	$router->post('valUsr',			'LogicController@valUsr');
?>