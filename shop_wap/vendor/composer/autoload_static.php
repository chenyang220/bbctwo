<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb09aba42e1cf8639df573e895b0081d3
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
    );

    public static $fallbackDirsPsr0 = array (
        0 => __DIR__ . '/../..' . '/shop/models',
        1 => __DIR__ . '/../..' . '/libraries',
        2 => __DIR__ . '/../..' . '/shop/controllers',
        3 => __DIR__ . '/../..' . '/libraries/Yf',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb09aba42e1cf8639df573e895b0081d3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb09aba42e1cf8639df573e895b0081d3::$prefixDirsPsr4;
            $loader->fallbackDirsPsr0 = ComposerStaticInitb09aba42e1cf8639df573e895b0081d3::$fallbackDirsPsr0;

        }, null, ClassLoader::class);
    }
}
