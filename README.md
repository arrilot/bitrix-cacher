[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-cacher/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-cacher/)

# Bitrix Cacher - обёртка над ядром Bitrix для более удобного кэширования PHP-переменных

## Установка

1. ```composer require arrilot/bitrix-cacher```
2. Регистрируем пакет в `init.php` - `Arrilot\BitrixCacher\ServiceProvider::register();`

## Использование

### Через метод

```php

use Arrilot\BitrixCacher\Cache;
use Arrilot\BitrixCacher\AbortCacheException;

$result = Cache::remember('cacheKeyHere', 3600, function () {
    $result = 0;
    for ($i = 0; $i < 20000000; $i++) {
        $result += $i;
    }
    
    if ( // something bad happened ) {
        // выполнит $obCache->AbortDataCache() и вернёт null в качестве $result
        throw new AbortCacheException();
    }

    return $result;
});

```

Для удобства рекомендуется добавить глобальный хэлпер:

```php
/**
 * @param null|string $key
 * @param null|int $seconds
 * @param null|Closure $callback
 * @param string $initDir
 * @param string $basedir
 * @return \Arrilot\BitrixCacher\CacheBuilder|mixed
 */
function cache($key = null, $seconds = null, $callback = null, $initDir = '/', $basedir = 'cache')
{
    if (func_num_args() === 0) {
        return new \Arrilot\BitrixCacher\CacheBuilder();
    }

    return \Arrilot\BitrixCacher\Cache::remember($key, $seconds, $callback, $initDir, $basedir);
}
```

и использовать его либо вместо `Cache::remember()`, либо как начало цепочки построения кэша CacheBuilder-а

Обратите внимание, что в отличии от `CPHPCache::InitCache()` (и его аналога из d7) по-умолчанию `$initDir = '/'`, а не false.
Это значит, что по-умолчанию кэш доступен для всего сайта.

### Через CacheBuilder

```php

$result = cache()
    ->key('cacheKeyHere')
    ->seconds(3600) // также доступны методы minutes(), hours(), days()
    ->initDir('/foo') // можно опустить если хотим использовать значение по-умолчанию
    ->baseDir('cache/foo') // можно опустить если хотим использовать значение по-умолчанию
    ->execute(function () {
        ...
        return ...;
    });
```

### Кэширование в php-переменную

В случаях, когда возможен многократный вызов одного и того же кэша (т.е. с одними и теми же параметрами key, initDir, baseDir) в течение выполнения одного скрипта,
разумно добавлять дополнительное кэширование в php переменную, чтобы не дергать внешнее хранилище с кэшем (файлы, memcache и т д) просто так несколько раз.
С использованием CacheBuilder это сделать очень просто - надо добавить `->enablePhpLayer()` в цепочку построения кэша.

```php

$result = cache()
    ->key('cacheKeyHere')
    ->seconds(3600)
    ->enablePhpLayer()
    ->execute(function () {
        ...
        return ...;
    });
```

Если есть потребность кэшировать вообще только в php-переменную (не трогая внешнее хранилище), то это делается вот так:

```php

$result = cache()
    ->key('cacheKeyHere')
    ->onlyPhpLayer()
    ->execute(function () {
        ...
        return ...;
    });
```

Время кэширования в этом случае уже, конечно, не имеет смысла.

### Аборт кэша

Если вы хотите отменить создание кэша в execute, то необходимо выкинуть исключение Arrilot\BitrixCacher\AbortCacheException
Цепочка execute при этом вернёт null, это значение можно изменить при помощи `->whenAbort([])->` или `->whenAbort(function () { return 'some message'; })->`

### Отладка

Пакет предоставляет дополнительное окно отладки в котором можно посмотреть
- сколько и каких мы сделали запросов в кэш,
- сколько хитов,
- сколько мисов,
- сколько запросов с нулевым TTL и которые не кэшируются, соответственно, вообще
