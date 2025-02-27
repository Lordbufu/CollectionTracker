<?php

// Depricated ??
use App\Core\{App, Container};

$container = new Container();

$container->bind('loader', function () {
    $config = require base_path('config.php');
    $loader = App::getMap('loader');

    return new $loader($config);
});

App::setContainer($container);