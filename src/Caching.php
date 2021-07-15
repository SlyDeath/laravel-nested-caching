<?php

namespace SlyDeath\NestedCaching;

use Illuminate\Contracts\Cache\Repository as Cache;

/**
 * Class Caching
 *
 * @package SlyDeath\NestedCaching
 */
class Caching
{
    /**
     * Cache instance
     *
     * @var Cache
     */
    protected Cache $cache;
    
    /**
     * Caching constructor
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache->tags(config('nested-caching.cache-tag'));
    }
    
    /**
     * Caching
     *
     * @param string|object $key Caching key
     * @param string $output Output
     * @param string|int|null $minutes Cache lifetime
     *
     * @return mixed
     */
    public function put($key, string $output, $minutes = null)
    {
        if ($minutes) {
            return $this->cache->remember($key, $minutes, fn() => $output);
        }
        
        return $this->cache->rememberForever($key, fn() => $output);
    }
    
    /**
     * Checking if key exists
     *
     * @param string $key Caching key
     *
     * @return boolean
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }
    
    /**
     * Checking if key exists
     *
     * @param string $key Caching key
     *
     * @return array|mixed
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $key)
    {
        return $this->cache->get($key);
    }
    
    /**
     * Deleting from the cache by key
     *
     * @param string|array $key Caching key
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function forget($key): bool
    {
        return is_array($key) ? $this->cache->deleteMultiple($key) : $this->cache->forget($key);
    }
}