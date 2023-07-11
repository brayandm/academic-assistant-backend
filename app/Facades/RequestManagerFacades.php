<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RequestManagerFacades extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'requestManager';
    }
}
