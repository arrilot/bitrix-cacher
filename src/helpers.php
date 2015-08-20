<?php

use Arrilot\BitrixCacher\Cache;

if (! function_exists('cache')) {
    /**
     * Remember closure's result in cache for a given amount of time.
     *
     * @param string $key
     * @param double $minutes
     * @param Closure $callback
     *
     * @return mixed
     */
    function cache($key, $minutes, Closure $callback)
    {
        return Cache::remember($key, $minutes, $callback);
    }
}
