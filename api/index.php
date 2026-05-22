<?php

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$tmpRoot = '/tmp/game-hub';
$tmpViewPath = $tmpRoot.'/storage/framework/views';
$tmpDatabasePath = $tmpRoot.'/database.sqlite';
$packagedDatabasePath = __DIR__.'/../database/vercel.sqlite';
$useTmpSqlite = getenv('VERCEL') && ! getenv('DB_HOST') && ! getenv('DB_URL');

foreach ([
    $tmpViewPath,
    $tmpRoot.'/storage/framework/cache/data',
    $tmpRoot.'/storage/framework/sessions',
] as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}

putenv('VIEW_COMPILED_PATH='.$tmpViewPath);
$_ENV['VIEW_COMPILED_PATH'] = $tmpViewPath;
$_SERVER['VIEW_COMPILED_PATH'] = $tmpViewPath;

if ($useTmpSqlite) {
    $databaseReady = false;

    if (! file_exists($tmpDatabasePath)) {
        if (file_exists($packagedDatabasePath)) {
            copy($packagedDatabasePath, $tmpDatabasePath);
            $databaseReady = true;
        } else {
            touch($tmpDatabasePath);
        }
    }

    foreach ([
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => $tmpDatabasePath,
        'SESSION_DRIVER' => 'array',
        'CACHE_STORE' => 'array',
        'QUEUE_CONNECTION' => 'sync',
    ] as $key => $value) {
        putenv($key.'='.$value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

$app = require_once __DIR__.'/../bootstrap/app.php';

if ($useTmpSqlite && ! $databaseReady && ! file_exists($tmpRoot.'/.database-ready')) {
    $app->make(ConsoleKernel::class)->call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);

    touch($tmpRoot.'/.database-ready');
}

$app->handleRequest(Request::capture());
