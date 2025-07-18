<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit845b6cb3ad059a70bd5a979f23153f88
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Newspack_Block_Theme\\Core' => __DIR__ . '/../..' . '/includes/class-core.php',
        'Newspack_Block_Theme\\Jetpack' => __DIR__ . '/../..' . '/includes/class-jetpack.php',
        'Newspack_Block_Theme\\Patterns' => __DIR__ . '/../..' . '/includes/class-patterns.php',
        'Newspack_Block_Theme\\Subtitle_Block' => __DIR__ . '/../..' . '/includes/blocks/subtitle-block/class-subtitle-block.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit845b6cb3ad059a70bd5a979f23153f88::$classMap;

        }, null, ClassLoader::class);
    }
}
