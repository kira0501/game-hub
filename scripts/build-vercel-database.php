<?php

$database = realpath(__DIR__.'/../database') ?: __DIR__.'/../database';
$path = $database.'/vercel.sqlite';

if (file_exists($path)) {
    unlink($path);
}

touch($path);

foreach ([
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => $path,
    'SESSION_DRIVER' => 'array',
    'CACHE_STORE' => 'array',
    'QUEUE_CONNECTION' => 'sync',
] as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

passthru(PHP_BINARY.' artisan migrate:fresh --seed --force', $code);

exit($code);
