<?php
	/* Routes voor de landings pagina */
	$router->get('',				'PagesController@landing');
	$router->post('register',		'LogicController@register');
	$router->post('login',			'LogicController@login');
	/* routes voor de beheer pagina */
		// default routes
	$router->get('beheer',			'PagesController@beheer');
	$router->post('beheer',			'LogicController@beheer');
		// Album routes
	$router->post('albumT',			'LogicController@albumT');
	$router->post('albumBew',		'LogicController@albumBew');
	$router->post('albumV',			'LogicController@albumV');
		// Serie routes
	$router->post('serieM',			'LogicController@serieM');
	$router->post('serieBek',		'LogicController@serieBek');
	$router->post('serieBew',		'LogicController@serieBew');
	$router->post('serieVerw',		'LogicController@serieVerw');
		// Overige routes
	$router->post('aReset',			'LogicController@adminReset');
	/* routes voor de gebruik pagina */
	$router->get('gebruik',			'PagesController@gebruik');
	$router->post('gebruik',		'LogicController@gebruik');
	$router->post('albSta',			'LogicController@albSta');
	/* Routes voor de beheer en gebruik pagina */
	$router->post('valUsr',			'LogicController@valUsr');
?>