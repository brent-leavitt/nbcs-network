<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit529f54965a84e46d53263e0f848b9ed5
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit529f54965a84e46d53263e0f848b9ed5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit529f54965a84e46d53263e0f848b9ed5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
