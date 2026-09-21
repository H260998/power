<?php

// Only disposable Laravel files belong on the function's writable filesystem.
$storage = sys_get_temp_dir().'/promax-storage';

foreach (['framework/cache/data', 'framework/sessions', 'framework/views', 'logs', 'bootstrap/cache'] as $directory) {
    $path = $storage.'/'.$directory;
    if (! is_dir($path) && ! mkdir($path, 0700, true) && ! is_dir($path)) {
        throw new RuntimeException('Unable to initialize temporary Laravel storage.');
    }
}

$defaults = [
    'LARAVEL_STORAGE_PATH' => $storage,
    'VIEW_COMPILED_PATH' => $storage.'/framework/views',
    'APP_SERVICES_CACHE' => $storage.'/bootstrap/cache/services.php',
    'APP_PACKAGES_CACHE' => $storage.'/bootstrap/cache/packages.php',
    'LOG_CHANNEL' => 'stderr',
];

foreach ($defaults as $name => $value) {
    if (getenv($name) === false) {
        putenv($name.'='.$value);
        $_ENV[$name] = $_SERVER[$name] = $value;
    }
}

require __DIR__.'/../public/index.php';
