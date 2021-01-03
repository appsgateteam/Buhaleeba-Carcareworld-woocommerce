<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5f021f80661cd057e11916919e81b538
{
    public static $files = array (
        'bee91f6e081cee6ae314324bd77cdd19' => __DIR__ . '/..' . '/ericmann/wp-session-manager/includes/deprecated.php',
        '94da49b8a6ca768bd9153ee879ff4877' => __DIR__ . '/..' . '/gambitph/titan-framework/titan-framework.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPAS_API\\' => 9,
        ),
        'E' => 
        array (
            'EAMann\\Sessionz\\' => 16,
        ),
        'D' => 
        array (
            'Defuse\\Crypto\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPAS_API\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/rest-api/includes',
        ),
        'EAMann\\Sessionz\\' => 
        array (
            0 => __DIR__ . '/..' . '/ericmann/sessionz/php',
        ),
        'Defuse\\Crypto\\' => 
        array (
            0 => __DIR__ . '/..' . '/defuse/php-encryption/src',
        ),
    );

    public static $classMap = array (
        'EAMann\\WPSession\\CacheHandler' => __DIR__ . '/..' . '/ericmann/wp-session-manager/includes/CacheHandler.php',
        'EAMann\\WPSession\\DatabaseHandler' => __DIR__ . '/..' . '/ericmann/wp-session-manager/includes/DatabaseHandler.php',
        'EAMann\\WPSession\\Objects\\Option' => __DIR__ . '/..' . '/ericmann/wp-session-manager/includes/Option.php',
        'EAMann\\WPSession\\OptionsHandler' => __DIR__ . '/..' . '/ericmann/wp-session-manager/includes/OptionsHandler.php',
        'EAMann\\WPSession\\SessionHandler' => __DIR__ . '/..' . '/ericmann/wp-session-manager/includes/SessionHandler.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5f021f80661cd057e11916919e81b538::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5f021f80661cd057e11916919e81b538::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5f021f80661cd057e11916919e81b538::$classMap;

        }, null, ClassLoader::class);
    }
}