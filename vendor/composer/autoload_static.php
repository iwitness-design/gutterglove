<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8a3f07b8d6d392e338c16b0227fe7ccd
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8a3f07b8d6d392e338c16b0227fe7ccd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8a3f07b8d6d392e338c16b0227fe7ccd::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}