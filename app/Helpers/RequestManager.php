<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Redis;

class RequestManager
{
    public function rateLimit(string $endpointName, int $rate, int $seconds)
    {
        $user = auth()->user();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated');
        }

        $key = $endpointName.':'.$user->id.':'.floor(Carbon::now()->timestamp / $seconds);

        if (Redis::setnx($key, 0)) {
            Redis::expire($key, $seconds * 2);
        }

        if (Redis::incr($key, 1) > $rate) {
            throw new AuthenticationException('Too many requests');
        }

        return true;
    }
}
