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

// One-time production initialization. The token is stored only in Vercel and
// this block is removed immediately after the database has been initialized.
if (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) === '/_deploy/initialize') {
    $expectedToken = getenv('DEPLOYMENT_MIGRATION_TOKEN');
    $providedToken = $_SERVER['HTTP_X_DEPLOYMENT_TOKEN'] ?? '';

    if (! is_string($expectedToken) || $expectedToken === '' || ! hash_equals($expectedToken, $providedToken)) {
        http_response_code(404);
        exit;
    }

    require __DIR__.'/../vendor/autoload.php';

    $app = require __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $commands = [
        ['migrate', ['--force' => true]],
        ['db:seed', ['--class' => 'Database\\Seeders\\CategorySeeder', '--force' => true]],
        ['db:seed', ['--class' => 'Database\\Seeders\\SettingSeeder', '--force' => true]],
        ['db:seed', ['--class' => 'Database\\Seeders\\AdminSeeder', '--force' => true]],
    ];

    $results = [];
    foreach ($commands as [$command, $arguments]) {
        $status = Illuminate\Support\Facades\Artisan::call($command, $arguments);
        $results[$command.($arguments['--class'] ?? '')] = $status;

        if ($status !== 0) {
            http_response_code(500);
            break;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($results, JSON_THROW_ON_ERROR);
    exit;
}

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $exception) {
    // Keep the final diagnostic short: Vercel truncates long exception traces.
    error_log(sprintf(
        'Laravel startup failed: %s: %s in %s:%d',
        $exception::class,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
    ));

    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Internal Server Error';
}
