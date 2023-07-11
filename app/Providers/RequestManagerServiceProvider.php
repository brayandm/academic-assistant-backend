<?php

namespace App\Providers;

use App\Helpers\RequestManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class RequestManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        App::bind('requestManager', function () {
            return new RequestManager();
        });
    }
}
