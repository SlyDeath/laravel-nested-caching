<?php

namespace SlyDeath\NestedCaching;

/**
 * Class CacheStack
 *
 * @package SlyDeath\NestedCaching
 */
class CacheStack
{
    /**
     * List of cache keys
     *
     * @var array $keys
     */
    protected array $keys = [];
    
    /**
     * List of another caches
     *
     * @var array $another_caches
     */
    protected array $another_caches = [];
    
    /**
     * Default key for storing another caches
     *
     * @var string
     */
    protected string $another_caches_key = 'slydeath:nc:ac';
    
    /**
     * Cache instance
     *
     * @var Caching $cache
     */
    protected Caching $cache;
    
    /**
     * CacheStack constructor
     *
     * @param Caching $caching
     */
    public function __construct(Caching $caching)
    {
        $this->cache = $caching;
    }
    
    /**
     * Add key to stack
     *
     * @param string $key Caching key
     */
    public function setKey(string $key): CacheStack
    {
        $this->keys[] = $key;
        
        return $this;
    }
    
    /**
     * Add another key
     *
     * @param string $key Caching key
     */
    public function setAnotherKey(string $key): CacheStack
    {
        $this->another_caches_key = $key;
        
        return $this;
    }
    
    /**
     * Get a list of keys
     *
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }
    
    /**
     * Collect another caches
     *
     * @param array $data Another cache data
     */
    public function addAnotherCache(array $data)
    {
        $this->another_caches[] = $data;
    }
    
    /**
     * Get list of another caches
     *
     * @return mixed
     */
    public function getAnotherCaches()
    {
        return \Cache::rememberForever($this->another_caches_key, fn() => $this->another_caches);
    }
    
    /**
     * Clearing cache stack
     *
     * @param string|array|null $keys Caching keys
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function clearCache($keys = null): bool
    {
        $keys = $keys ?? $this->getKeys();
        
        $this->keys = [];
        
        return $this->cache->forget($keys);
    }
    
    /**
     * Clear another caches
     */
    public function clearAnotherCaches()
    {
        foreach ($this->getAnotherCaches() as $cache) {
            $tags = data_get($cache, 'tags');
            $key  = data_get($cache, 'key');
            
            if (count($tags)) {
                \Cache::tags($tags)->forget($key);
            } else {
                \Cache::forget($key);
            }
        }
    }
}
