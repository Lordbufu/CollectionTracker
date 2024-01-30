<?php
	$router->get('',				'PagesController@landing');
	$router->post('register',		'LogicController@register');
	$router->post('login',			'LogicController@login');
	$router->get('beheer',			'PagesController@beheer');
	$router->post('beheer',			'LogicController@beheer');
	$router->post('albumT',			'LogicController@albumT');
	$router->post('albumBew',		'LogicController@albumBew');
	$router->post('albumV',			'LogicController@albumV');
	$router->post('serieM',			'LogicController@serieM');
	$router->post('serieBek',		'LogicController@serieBek');
	$router->post('serieBew',		'LogicController@serieBew');
	$router->post('serieVerw',		'LogicController@serieVerw');
	$router->post('aReset',			'LogicController@adminReset');
	$router->get('gebruik',			'PagesController@gebruik');
	$router->post('gebruik',		'LogicController@gebruik');
	$router->post('albSta',			'LogicController@albSta');
	$router->post('valUsr',			'LogicController@valUsr');

	// test route
	$router->get('test', 'PagesController@test');
	$router->post('isbn', 'LogicController@isbn');
	$router->get('test2', 'PagesController@test2');
?>