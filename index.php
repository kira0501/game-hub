<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

if (isset($_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'])) {
    $basePath = str_replace('\\', '/', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));

    if ($basePath && $basePath !== '/' && str_starts_with($_SERVER['REQUEST_URI'], $basePath)) {
        $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($basePath)) ?: '/';
    }
}

$app->handleRequest(Illuminate\Http\Request::capture());
