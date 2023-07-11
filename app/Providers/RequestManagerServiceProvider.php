<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use App\Helpers\RequestManager;

class RequestManagerServiceProvider extends ServiceProvider {

   public function boot() {
      //
   }

   public function register() {
    App::bind('requestManager',function() {
        return new RequestManager();
    });
   }
}
