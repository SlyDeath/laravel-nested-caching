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
    public function setKey(string $key)
    {
        $this->keys[] = $key;
    }
    
    /**
     * Clearing the current cache stack
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function clearCache(): bool
    {
        $keys       = $this->getKeys();
        $this->keys = [];
        
        return $this->cache->forget($keys);
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
}
