<?php

use Arrilot\BitrixCacher\Cache;

if (! function_exists('cache')) {
    /**
     * Store closure's result in the cache for a given number of minutes.
     *
     * @param string $key
     * @param double $minutes
     * @param Closure $callback
     * @param bool|string $initDir
     *
     * @return mixed
     */
    function cache($key, $minutes, Closure $callback, $initDir = false, $basedir = "cache")
    {
        return Cache::remember($key, $minutes, $callback, $initDir, $basedir);
    }
}
