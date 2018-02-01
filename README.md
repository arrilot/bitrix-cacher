[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-cacher/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-cacher/)

# Обёртка над ядром Битрикса для более удобного кэширования php переменных

## Установка

```composer require arrilot/bitrix-cacher```

## Использование

```php

use Arrilot\BitrixCacher\Cache;
use Arrilot\BitrixCacher\AbortCacheException;

$result = Cache::remember('cacheKeyHere', 30, function () {
    $result = 0;
    for ($i = 0; $i < 20000000; $i++) {
        $result += $i;
    }
    
    if ( // something bad happenned ) {
        // выполнит `$obCache->AbortDataCache()` и вернёт null в качестве $result
        throw new AbortCacheException();
    }

    return $result;
});

```

Для удобства рекомендуется добавить глобальный хэлпер:

```php
function cache($key, $minutes, Closure $callback, $initDir = '/', $basedir = 'cache')
{
    return Cache::remember($key, $minutes, $callback, $initDir, $basedir);
}
```

и использовать его вместо `Cache::remember()`

Обратите внимание, что в отличии от `CPHPCache::InitCache()` (и его аналога из d7) по-умолчанию `$initDir = '/'`, а не false.
Это значит, что по-умолчанию кэш доступен для всего сайта.
