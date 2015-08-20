<?php

namespace Arrilot\BitrixCacher;

use Closure;
use CPHPCache;

class Cache
{
    /**
     * Remember closure's result in cache for a given amount of time.
     *
     * @param string $key
     * @param double $minutes
     * @param Closure $callback
     *
     * @return mixed
     */
    public static function remember($key, $minutes, Closure $callback)
    {
        $obCache = new CPHPCache();
        if ($obCache->initCache($minutes*60, $key)) {
            $vars = $obCache->getVars();

            return $vars['cache'];
        }

        $obCache->startDataCache();
        $cache = $callback();
        $obCache->endDataCache(['cache' => $cache]);

        return $cache;
    }

    /**
     * Remember closure's result in cache for a long time.
     *
     * @param string $key
     * @param Closure $callback
     *
     * @return mixed
     */
    public static function rememberForever($key, Closure $callback)
    {
        return static::remember($key, 99999999, $callback);
    }
}