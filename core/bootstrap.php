<?php

use App\Core\{App, Processing};
use App\Core\Database\{QueryBuilder, Connection};


App::bind('config', require '../config.php');

$config = App::get('config');

App::bind('database', new QueryBuilder (
        Connection::make(App::get('config')['database'])
));

App::bind('processing', new Processing);

?>