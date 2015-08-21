<?php

namespace Arrilot\BitrixCacher;

use Closure;
use CPHPCache;

class Cache
{
    /**
     * Store closure's result in the cache for a given number of minutes.
     *
     * @param string  $key
     * @param double  $minutes
     * @param Closure $callback
     * @param bool|string $initDir
     *
     * @return mixed
     */
    public static function remember($key, $minutes, Closure $callback, $initDir = false)
    {
        $minutes = (double) $minutes;
        if ($minutes <= 0) {
            return $callback();
        }

        $obCache = new CPHPCache();
        if ($obCache->initCache($minutes*60, $key, $initDir)) {
            $vars = $obCache->getVars();

            return $vars['cache'];
        }

        $obCache->startDataCache();
        $cache = $callback();
        $obCache->endDataCache(['cache' => $cache]);

        return $cache;
    }

    /**
     * Store closure's result in the cache for a long time.
     *
     * @param string $key
     * @param Closure $callback
     * @param bool|string $initDir
     *
     * @return mixed
     */
    public static function rememberForever($key, Closure $callback, $initDir = false)
    {
        return static::remember($key, 99999999, $callback, $initDir);
    }

    /**
     * Flush all cache or cache for a specified dir.
     *
     * @param bool $initDir
     *
     * @return bool
     */
    public function flush($initDir = false)
    {
        return bXClearCache(true, $initDir ? $initDir : '');
    }
}