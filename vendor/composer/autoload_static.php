<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit31f43657d75e0e559ee11f8a9fa6b976
{
    public static $files = array (
        '5be0ed2ab2ae20e14a0b26be5c4dd383' => __DIR__ . '/..' . '/mobily-ws/api/MobilySms.php',
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WCGQL\\Translators\\' => 18,
            'WCGQL\\Models\\' => 13,
            'WCGQL\\Mobile\\' => 13,
            'WCGQL\\Helpers\\' => 14,
        ),
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
        'G' => 
        array (
            'GraphQL\\' => 8,
        ),
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WCGQL\\Translators\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Translators',
        ),
        'WCGQL\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Models',
        ),
        'WCGQL\\Mobile\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Mobile',
        ),
        'WCGQL\\Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/Helpers',
        ),
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/src/Twilio',
        ),
        'GraphQL\\' => 
        array (
            0 => __DIR__ . '/..' . '/webonyx/graphql-php/src',
        ),
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit31f43657d75e0e559ee11f8a9fa6b976::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit31f43657d75e0e559ee11f8a9fa6b976::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit31f43657d75e0e559ee11f8a9fa6b976::$classMap;

        }, null, ClassLoader::class);
    }
}
