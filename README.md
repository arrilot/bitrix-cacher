[![Latest Stable Version](https://poser.pugx.org/arrilot/bitrix-models/v/stable.svg)](https://packagist.org/packages/arrilot/bitrix-cacher/)
[![Total Downloads](https://img.shields.io/packagist/dt/arrilot/bitrix-models.svg?style=flat)](https://packagist.org/packages/Arrilot/bitrix-cacher)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/arrilot/bitrix-models/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/arrilot/bitrix-cacher/)

#Bitrix cacher (in development)

#Пример

```php
use Arrilot\BitrixCacher\Cache;

$result = Cache::remember('test4', 1, function () {
    $result = 0;
    for ($i = 0; $i < 20000000; $i++) {
        $result += $i;
    }

    return $result;
});

```