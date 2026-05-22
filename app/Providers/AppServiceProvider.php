<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        if (! app()->runningInConsole()) {
            $baseUrl = request()->getSchemeAndHttpHost();
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

            if (str_starts_with($scriptName, '/laravel1/')) {
                $baseUrl .= '/laravel1';
            }

            URL::forceRootUrl($baseUrl);
        }
    }
}
