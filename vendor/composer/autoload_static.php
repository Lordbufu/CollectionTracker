<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita497427bd7695e03c8e90e71a8328ac1
{
    public static $classMap = array (
        'App\\Controllers\\LogicController' => __DIR__ . '/../..' . '/app/controllers/LogicController.php',
        'App\\Controllers\\PagesController' => __DIR__ . '/../..' . '/app/controllers/PagesController.php',
        'App\\Core\\App' => __DIR__ . '/../..' . '/core/App.php',
        'App\\Core\\Database\\Connection' => __DIR__ . '/../..' . '/core/database/Connection.php',
        'App\\Core\\Database\\QueryBuilder' => __DIR__ . '/../..' . '/core/database/QueryBuilder.php',
        'App\\Core\\Processing' => __DIR__ . '/../..' . '/core/Processing.php',
        'App\\Core\\Request' => __DIR__ . '/../..' . '/core/Request.php',
        'App\\Core\\Router' => __DIR__ . '/../..' . '/core/Router.php',
        'ComposerAutoloaderInita497427bd7695e03c8e90e71a8328ac1' => __DIR__ . '/..' . '/composer/autoload_real.php',
        'Composer\\Autoload\\ClassLoader' => __DIR__ . '/..' . '/composer/ClassLoader.php',
        'Composer\\Autoload\\ComposerStaticInita497427bd7695e03c8e90e71a8328ac1' => __DIR__ . '/..' . '/composer/autoload_static.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInita497427bd7695e03c8e90e71a8328ac1::$classMap;

        }, null, ClassLoader::class);
    }
}
