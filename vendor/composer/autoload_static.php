<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit820c6a05102b2b623523e25c6d935882
{
    public static $files = array (
        '1cfd2761b63b0a29ed23657ea394cb2d' => __DIR__ . '/..' . '/topthink/think-captcha/src/helper.php',
        'ddc3cd2a04224f9638c5d0de6a69c7e3' => __DIR__ . '/..' . '/topthink/think-migration/src/config.php',
    );

    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\migration\\' => 16,
            'think\\composer\\' => 15,
            'think\\captcha\\' => 14,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
        'P' => 
        array (
            'Phinx\\' => 6,
            'Pay\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\migration\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-migration/src',
        ),
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'think\\captcha\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-captcha/src',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
        'Phinx\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-migration/phinx/src/Phinx',
        ),
        'Pay\\' => 
        array (
            0 => __DIR__ . '/..' . '/zoujingli/pay-php-sdk/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPQRCode' => 
            array (
                0 => __DIR__ . '/..' . '/aferrandini/phpqrcode/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit820c6a05102b2b623523e25c6d935882::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit820c6a05102b2b623523e25c6d935882::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit820c6a05102b2b623523e25c6d935882::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
