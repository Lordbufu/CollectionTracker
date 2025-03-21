<?php
/* Routes for the public landing page. */
$router->get('/',			'index.php');                               // Landing-page for all other types of users (that dont get the 'guest' tag).

/* Authenticated user homepage routes. */
$router->get('/home',		'home/view.php')->only('guest');            // Initial Source = 'http\controllers\index.php'
$router->post('/home',		'home/view.php')->only('guest');            // Source = 'http\views\home\popins\guest\' (pop-in close routes)
$router->get('/register',	'redir/reg.pop.php')->only('guest');        // Source = 'http\views\home\templates\guest\banner-subgrid.php'
$router->get('/login',		'redir/login.pop.php')->only('guest');      // Source = 'http\views\home\templates\guest\banner-subgrid.php'
$router->put('/register',	'user/store.php')->only('guest');           // Source = 'http\views\home\popin\register.pop-in.php'
$router->post('/login',		'user/login.php')->only('guest');           // Source = 'http\views\home\popin\login.pop-in.php'

/* Routes for the regular users only. */
$router->get('/gebruik',	'home/gebruik.view.php')->only('user');     // Initial Source = 'http\controllers\user\login.php'
$router->post('/gebruik',	'home/gebruik.view.php')->only('user');     // Various sources, mostly pop-in close buttons.
$router->put('/colAdd',     'collectie/add.php')->only('user');         // Source = 'http\views\home\template\user\table-subgrid.php' (add/remove switch)
$router->delete('/colRem',	'collectie/remove.php')->only('user');      // Source = 'http\views\home\template\user\table-subgrid.php' (add/remove switch)

/* Routes for the admin users only. */
$router->get('/beheer',		'home/beheer.view.php')->only('admin');		// Initial Source = 'http\controllers\user\login.php'
$router->post('/beheer',	'home/beheer.view.php')->only('admin');		// Various sources, mostly return\close buttons.
$router->get('/wwReset',	'redir/reset.pop.php')->only('admin');      // Source = 'http\views\home\template\admin\banner-subgrid.php' (Wachtw Reset knop)
$router->patch('/aReset',	'user/reset.php')->only('admin');           // Source = 'http\views\home\popins\admin\wachtwoord-reset-pop-in.php' (form submit button)

/* Admin reeks actions */
    /* Admin reeks maken. */
$router->post('/reeksPop',	'redir/reeks.pop.php')->only('admin');      // Source = 'http\views\home\template\admin\controler-subgrid.php'  (reeks maken controller)
$router->put('/reeksM',	    'reeks/add.php')->only('admin');            // Source = 'http\views\home\pop-ins\admin\reeks-maken-pop-in.php'  (reeks maken form)

    /* Admin reeks bewerken. */
$router->patch('/rEdit',	'redir/reeks.pop.php')->only('admin');      // Source = 'http\views\home\template\admin\reeks-table-subgrid.php' (reeks bewerken knop)
$router->patch('/reeksM',	'reeks/edit.php')->only('admin');           // Source = 'http\views\home\pop-ins\admin\reeks-maken-pop-in.php'  (reeks bewerken form)

    /* Admin reeks and alle bijbehorende links verwijderen. */
$router->delete('/rDel',	'reeks/delete.php')->only('admin');         // Source = 'http\views\home\template\admin\reeks-table-subgrid.php' (reeks verwijderen knop)

/* Admin items actions */
    /* Admin items maken. */
$router->post('/itemsPop',	'redir/items.pop.php')->only('admin');      // Source = 'http\views\home\template\admin\controler-subgrid.php' (item toevoegen controller)
$router->put('/itemsM',	    'items/add.php')->only('admin');            // Source = 'http\views\home\pop-ins\admin\items-maken-pop-in.php' (item maken form)

    /* Admin items bewerken. */
$router->patch('/iEdit',	'redir/items.pop.php')->only('admin');      // Source = 'http\views\home\templates\admin\items-table-subgrid.php' (item bewerken knop)
$router->patch('/itemsM',	'items/edit.php')->only('admin');           // Source = 'http\views\home\pop-ins\admin\items-maken-pop-in.php'  (item bewerken form)

    /* Admin item verwijderen. */
$router->delete('/iDel',	'items/delete.php')->only('admin');         // Source = 'http\views\home\templates\admin\items-table-subgrid.php' (item verwijderen knop)

/* Admin Isbn routes */
$router->put('/iIsbnS',     'scan/search.php')->only('admin');          // Source = 'http\views\home\pop-ins\admin\items-maken-pop-in.php' (create item -> isbn zoek knop)
$router->patch('/iIsbnS',   'scan/search.php')->only('admin');          // Source = 'http\views\home\pop-ins\admin\items-maken-pop-in.php' (edit item -> isbn zoek knop)
$router->post('/scanConf',  'scan/confirm.php')->only('admin');         // Source = 'http\views\home\pop-ins\isbn-preview-pop-in.php' (bevestigen knop)

/* Shared routes for all logged in user pages. */
$router->get('/logout',		'user/logout.php')->only('auth');           // 'Afmelden' banner menu button.
$router->post('/selReeks',	'reeks/get.php')->only('auth');             // Select/View a reeks buttons.
$router->post('/scanPop',	'redir/scan.pop.php')->only('auth');        // The barcode scan pop-in trigger route.
$router->post('/bCodeScan',	'scan/get.php')->only('auth');              // Barcode scan pop-in scan-trigger

/* Test route for showing album details on mobile devices */
$router->post('details',		'mobile/test.php')->only('user');