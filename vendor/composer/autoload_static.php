<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita497427bd7695e03c8e90e71a8328ac1
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
        ),
        'D' => 
        array (
            'Detection\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Detection\\' => 
        array (
            0 => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src',
        ),
    );

    public static $classMap = array (
        'App\\Controllers\\LogicController' => __DIR__ . '/../..' . '/app/controllers/LogicController.php',
        'App\\Controllers\\PagesController' => __DIR__ . '/../..' . '/app/controllers/PagesController.php',
        'App\\Core\\App' => __DIR__ . '/../..' . '/core/App.php',
        'App\\Core\\Collection' => __DIR__ . '/../..' . '/core/Collection.php',
        'App\\Core\\Database\\Connection' => __DIR__ . '/../..' . '/core/database/Connection.php',
        'App\\Core\\Database\\QueryBuilder' => __DIR__ . '/../..' . '/core/database/QueryBuilder.php',
        'App\\Core\\Isbn' => __DIR__ . '/../..' . '/core/Isbn.php',
        'App\\Core\\Request' => __DIR__ . '/../..' . '/core/Request.php',
        'App\\Core\\Router' => __DIR__ . '/../..' . '/core/Router.php',
        'App\\Core\\SessionMan' => __DIR__ . '/../..' . '/core/SessionMan.php',
        'App\\Core\\User' => __DIR__ . '/../..' . '/core/User.php',
        'ComposerAutoloaderInita497427bd7695e03c8e90e71a8328ac1' => __DIR__ . '/..' . '/composer/autoload_real.php',
        'Composer\\Autoload\\ClassLoader' => __DIR__ . '/..' . '/composer/ClassLoader.php',
        'Composer\\Autoload\\ComposerStaticInita497427bd7695e03c8e90e71a8328ac1' => __DIR__ . '/..' . '/composer/autoload_static.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Detection\\Cache\\Cache' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src/Cache/Cache.php',
        'Detection\\Cache\\CacheException' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src/Cache/CacheException.php',
        'Detection\\Cache\\CacheItem' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src/Cache/CacheItem.php',
        'Detection\\Exception\\MobileDetectException' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src/Exception/MobileDetectException.php',
        'Detection\\MobileDetect' => __DIR__ . '/..' . '/mobiledetect/mobiledetectlib/src/MobileDetect.php',
        'Psr\\SimpleCache\\CacheException' => __DIR__ . '/..' . '/psr/simple-cache/src/CacheException.php',
        'Psr\\SimpleCache\\CacheInterface' => __DIR__ . '/..' . '/psr/simple-cache/src/CacheInterface.php',
        'Psr\\SimpleCache\\InvalidArgumentException' => __DIR__ . '/..' . '/psr/simple-cache/src/InvalidArgumentException.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita497427bd7695e03c8e90e71a8328ac1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita497427bd7695e03c8e90e71a8328ac1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita497427bd7695e03c8e90e71a8328ac1::$classMap;

        }, null, ClassLoader::class);
    }
}
