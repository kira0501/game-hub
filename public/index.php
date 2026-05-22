<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

if (isset($_SERVER['SCRIPT_NAME'], $_SERVER['REQUEST_URI'])) {
    $scriptBase = str_replace('\\', '/', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
    $projectBase = str_replace('\\', '/', rtrim(dirname($scriptBase), '/\\'));

    foreach ([$scriptBase, $projectBase] as $basePath) {
        if ($basePath && $basePath !== '/' && str_starts_with($_SERVER['REQUEST_URI'], $basePath)) {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($basePath)) ?: '/';
            break;
        }
    }
}

$app->handleRequest(Request::capture());
