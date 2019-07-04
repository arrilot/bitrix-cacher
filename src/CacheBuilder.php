<?php

namespace Arrilot\BitrixCacher;

use Bitrix\Main\Data\StaticHtmlCache;
use Closure;
use CPHPCache;
use LogicException;

class CacheBuilder
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $seconds;

    /**
     * @var string
     */
    protected $initDir;

    /**
     * @var string
     */
    protected $baseDir;
    
    /**
     * @var PhpCache
     */
    protected $phpCache;

    /**
     * @var bool
     */
    protected $phpLayer;

    /**
     * @var bool
     */
    protected $onlyPhpLayer;

    /**
     * @var mixed
     */
    protected $whenAbort;

    public function __construct()
    {
        $this->restoreDefaults();
    }

    /**
     * Setter for key.
     * @param $key
     * @return CacheBuilder
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }
    
    /**
     * Setter for time.
     * @param int $seconds
     * @return CacheBuilder
     */
    public function seconds($seconds)
    {
        $this->seconds = $seconds;

        return $this;
    }
    
    /**
     * Setter for time.
     * @param double|int $minutes
     * @return CacheBuilder
     */
    public function minutes($minutes)
    {
        $this->seconds = intval($minutes * 60);

        return $this;
    }

    /**
     * Setter for time.
     * @param double|int $hours
     * @return CacheBuilder
     */
    public function hours($hours)
    {
        $this->seconds = intval($hours * 60 * 60);

        return $this;
    }

    /**
     * Setter for time.
     * @param double|int $days
     * @return CacheBuilder
     */
    public function days($days)
    {
        $this->seconds = intval($days * 60 * 60 * 24);

        return $this;
    }

    /**
     * Setter for initDir.
     * @param $dir
     * @return CacheBuilder
     */
    public function initDir($dir)
    {
        $this->initDir = $dir;

        return $this;
    }

    /**
     * Setter for whenAbort. Value or callable
     * @param mixed $value
     * @return CacheBuilder
     */
    public function whenAbort($value)
    {
        $this->whenAbort= $value;

        return $this;
    }

    /**
     * Setter for initDir.
     * @param $dir
     * @return CacheBuilder
     */
    public function baseDir($dir)
    {
        $this->baseDir = $dir;

        return $this;
    }

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function execute(Closure $callback)
    {
        if ($this->phpLayer || $this->onlyPhpLayer) {
            $key = $this->constructPhpCacheKey();
            if ($this->phpCache->has($key)) {
                return $this->phpCache->get($key);
            }
        }

        if ($this->onlyPhpLayer) {
            return $this->executeWithPhpCache($callback);
        }

        if (is_null($this->key)) {
            throw new LogicException('Key is not set.');
        }

        if (is_null($this->seconds)) {
            throw new LogicException('Time is not set.');
        }

        $result = Cache::remember($this->key, $this->seconds, $callback, $this->initDir, $this->baseDir, $this->whenAbort);
        if ($this->phpLayer) {
            $this->phpCache->put($this->constructPhpCacheKey(), $result);
        }

        return $result;
    }

    public function restoreDefaults()
    {
        $this->key = null;
        $this->seconds = null;
        $this->initDir = '/';
        $this->baseDir = 'cache';
        $this->phpLayer = false;
        $this->onlyPhpLayer = false;
        $this->phpCache = PhpCache::getInstance();
        $this->whenAbort = null;

        return $this;
    }
    
    /**
     * Enable cache in php variable.
     *
     * @param bool $value
     * @return $this
     */
    public function enablePhpLayer($value = true)
    {
        $this->phpLayer = $value;

        return $this;
    }
    
    /**
     * Enable cache in php variable.
     *
     * @param bool $value
     * @return $this
     */
    public function onlyPhpLayer($value = true)
    {
        $this->onlyPhpLayer = $value;

        return $this;
    }

    /**
     * @return string
     */
    protected function constructPhpCacheKey()
    {
        return json_encode([$this->key, $this->initDir, $this->baseDir]);
    }

    /**
     * @param Closure $callback
     * @return mixed
     */
    protected function executeWithPhpCache(Closure $callback)
    {
        $key = $this->constructPhpCacheKey();

        try {
            $result = $callback();
        } catch (AbortCacheException $e) {
            $result = null;
        }
        $this->phpCache->put($key, $result);

        return $result;
    }
}
