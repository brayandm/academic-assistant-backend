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
            return false;
        }

        return true;
    }

    public function threadLimit(string $task, int $threads, int $expiration)
    {
        $user = auth()->user();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated');
        }

        $key = $task.':'.$user->id.":"."threads";

        Redis::setnx($key, 0);

        Redis::expire($key, $expiration);

        if (Redis::incr($key, 1) > $threads) {
            Redis::decr($key, 1);
            return false;
        }

        return true;
    }

    public function releaseThread(string $task)
    {
        $user = auth()->user();

        if (! $user) {
            throw new AuthenticationException('Unauthenticated');
        }

        $key = $task.':'.$user->id.":"."threads";

        Redis::decr($key, 1);
    }
}
