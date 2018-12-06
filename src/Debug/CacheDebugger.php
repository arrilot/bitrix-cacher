<?php

namespace Arrilot\BitrixCacher\Debug;

class CacheDebugger
{
    /**
     * @var array
     */
    protected static $cacheTracks;
    
    public static function track($name, $initDir, $basedir, $key, $result)
    {
        $size = strlen(@serialize($result));
        static::$cacheTracks[] = compact('name', 'initDir', 'basedir', 'key', 'size');
    }
    
    public static function getCacheTracksGrouped($name)
    {
        $tracks = [];
        foreach (static::$cacheTracks as $track) {
            if ($track['name'] !== $name) {
                continue;
            }

            $hash = json_encode([$track['key'], $track['initDir'], $track['basedir']]);
            if (isset($tracks[$hash])) {
                $tracks[$hash]['count']++;
                $tracks[$hash]['size'] += $track['size'];
            } else {
                $tracks[$hash] = $track;
                $tracks[$hash]['count'] = 1;
            }
        }

        return $tracks;
    }
    
    public static function getTracksCount()
    {
        return count(static::$cacheTracks);
    }
    
    public static function onAfterEpilogHandler()
    {
        global $USER;

        $bExcel = isset($_REQUEST["mode"]) && $_REQUEST["mode"] === 'excel';
        if (!defined("ADMIN_AJAX_MODE") && !defined('PUBLIC_AJAX_MODE') && !$bExcel) {
            $bShowCacheStat = (\Bitrix\Main\Data\Cache::getShowCacheStat() && ($USER->CanDoOperation('edit_php') || $_SESSION["SHOW_CACHE_STAT"]=="Y"));
            if ($bShowCacheStat) {
                require_once(__DIR__.'/debug_info.php');
            }
        }
    }
}
