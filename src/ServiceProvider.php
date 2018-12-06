<?php

namespace Arrilot\BitrixCacher;

use Arrilot\BitrixCacher\Debug\CacheDebugger;

class ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public static function register()
    {
        $em = \Bitrix\Main\EventManager::getInstance();
        $em->addEventHandler('main', 'OnAfterEpilog', [CacheDebugger::class, 'onAfterEpilogHandler']);
    }
}
