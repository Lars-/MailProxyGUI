<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite8afbf63fa46405aada744d0d51dd27b
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LJPc\\MailProxyGui\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LJPc\\MailProxyGui\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite8afbf63fa46405aada744d0d51dd27b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite8afbf63fa46405aada744d0d51dd27b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite8afbf63fa46405aada744d0d51dd27b::$classMap;

        }, null, ClassLoader::class);
    }
}