<?php

namespace Arrilot\BitrixCacher;

use Bitrix\Main\Data\StaticHtmlCache;
use Closure;
use CPHPCache;

class Cache
{
    /**
     * Store closure's result in the cache for a given number of minutes.
     *
     * @param string $key
     * @param double $minutes
     * @param Closure $callback
     * @param bool|string $initDir
     * @param string $basedir
     * @return mixed
     */
    public static function remember($key, $minutes, Closure $callback, $initDir = '/', $basedir = 'cache')
    {
        $minutes = (double) $minutes;
        if ($minutes <= 0) {
            return $callback();
        }

        $obCache = new CPHPCache();
        if ($obCache->InitCache($minutes*60, $key, $initDir, $basedir)) {
            $vars = $obCache->GetVars();

            return $vars['cache'];
        }

        $obCache->StartDataCache();
        try {
            $cache = $callback();
            $obCache->EndDataCache(['cache' => $cache]);
        } catch (AbortCacheException $e) {
            $obCache->AbortDataCache();
            $cache = null;
        }

        return $cache;
    }

    /**
     * Store closure's result in the cache for a long time.
     *
     * @param string $key
     * @param Closure $callback
     * @param bool|string $initDir
     * @param string $basedir
     * @return mixed
     */
    public static function rememberForever($key, Closure $callback, $initDir = '/', $basedir = 'cache')
    {
        return static::remember($key, 99999999, $callback, $initDir, $basedir);
    }

    /**
     * Flush cache for a specified dir.
     *
     * @param string $initDir
     *
     * @return bool
     */
    public static function flush($initDir = "")
    {
        return BXClearCache(true, $initDir);
    }

    /**
     * Flushes all bitrix cache.
     *
     * @return void
     */
    public static function flushAll()
    {
        $GLOBALS["CACHE_MANAGER"]->cleanAll();
        $GLOBALS["stackCacheManager"]->cleanAll();
        $staticHtmlCache = StaticHtmlCache::getInstance();
        $staticHtmlCache->deleteAll();
        BXClearCache(true);
    }
}
