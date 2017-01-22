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
        throw new AbortCacheException();
    }

    return $result;
});

```

Также для удобства вместо ```Cache::remember()``` можно использовать хэлпер ```cache()```
