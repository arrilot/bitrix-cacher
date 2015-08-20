[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-cacher/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-cacher/)

#Bitrix cacher (in development)

#Пример

```php
use Arrilot\BitrixCacher\Cache;

$result = Cache::remember('cacheKeyHere', 30, function () {
    $result = 0;
    for ($i = 0; $i < 20000000; $i++) {
        $result += $i;
    }

    return $result;
});

```