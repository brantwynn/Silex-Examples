<?php

// autoload.php generated by Composer
if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
    require __DIR__.'/ClassLoader.php';
}

return call_user_func(function() {
    $loader = new \Composer\Autoload\ClassLoader();

    $map = require __DIR__.'/autoload_namespaces.php';

    foreach ($map as $namespace => $path) {
        $loader->add($namespace, $path);
    }

    $classMap = require __DIR__.'/autoload_classmap.php';
    if ($classMap) {
        $loader->addClassMap($classMap);
    }

    $loader->register();

    return $loader;
});
