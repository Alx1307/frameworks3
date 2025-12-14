<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\CmsHelper;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        CmsHelper::bladeDirective();
    }
    
    public function register(): void
    {
        $this->app->singleton('cms', function () {
            return new CmsHelper();
        });
    }
}