<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$tmpViewPath = '/tmp/game-hub/storage/framework/views';

if (! is_dir($tmpViewPath)) {
    mkdir($tmpViewPath, 0777, true);
}

putenv('VIEW_COMPILED_PATH='.$tmpViewPath);
$_ENV['VIEW_COMPILED_PATH'] = $tmpViewPath;
$_SERVER['VIEW_COMPILED_PATH'] = $tmpViewPath;

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
