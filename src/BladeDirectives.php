<?php

namespace SlyDeath\NestedCaching;

/**
 * Class BladeDirectives
 *
 * @package SlyDeath\NestedCaching
 */
class BladeDirectives
{
    /**
     * List of cache keys
     *
     * @var array $keys
     */
    protected array $keys = [];
    
    /**
     * List of minutes
     *
     * @var array $minutes
     */
    protected array $minutes = [];
    
    /**
     * Caching instance
     *
     * @var Caching $cache
     */
    protected Caching $cache;
    
    /**
     * BladeDirectives constructor
     *
     * @param Caching $caching Caching
     */
    public function __construct(Caching $caching)
    {
        $this->cache = $caching;
    }
    
    /**
     * Get cache data by key
     *
     * @param string $key Caching key
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getCache(string $key)
    {
        return $this->cache->get($key);
    }
    
    /**
     * Directive @cache
     *
     * @param string|object $key Caching key
     * @param string|int|null $minutes Cache lifetime
     *
     * @return bool
     *
     * @throws \Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function cache($key, $minutes = null): bool
    {
        ob_start();
        
        return $this->cache->has($this->applyData($key, $minutes));
    }
    
    /**
     * Cache key handling
     *
     * @param string|object $key Caching key
     * @param string|int|null $minutes Cache lifetime
     *
     * @return string
     *
     * @throws \Exception
     */
    public function applyData($key, $minutes = null): string
    {
        switch (true) {
            // Handling the key specified manually
            case(is_string($key)):
                $key = trim($key);
                break;
            
            // Trying to get the model key using the getNestedCacheKey method
            case(is_object($key) && method_exists($key, 'getNestedCacheKey')):
                $key = $key->getNestedCacheKey();
                break;
            
            // If this is a collection, then for the cache key we use the hash of its contents
            case($key instanceof \Illuminate\Support\Collection):
                $key = sha1($key);
                break;
            
            default:
                throw new NotDetermineKeyException('Could not determine an appropriate cache key');
        }
        
        $this->setKey($key);
        $this->setMinutes($minutes);
        
        // Add key to stack
        app(CacheStack::class)->setKey($key);
        
        return $key;
    }
    
    /**
     * Adding a cache key to the key list
     *
     * @param string $key Caching key
     */
    public function setKey(string $key)
    {
        $this->keys[] = $key;
    }
    
    /**
     * Directive @endcache
     *
     * @return mixed
     */
    public function endCache()
    {
        return $this->cache->put($this->getKey(), ob_get_clean(), $this->getMinutes());
    }
    
    /**
     * Retrieving a cache key from a list of keys
     *
     * @return string
     */
    public function getKey(): string
    {
        return array_pop($this->keys);
    }
    
    /**
     * Getting minutes from a list of minutes
     *
     * @return string|int|null
     */
    public function getMinutes()
    {
        return array_pop($this->minutes);
    }
    
    /**
     * Adding minutes to the minutes list
     *
     * @param string|int|null $minutes Cache lifetime
     */
    public function setMinutes($minutes = null)
    {
        $this->minutes[] = $minutes ? now()->addMinutes($minutes) : null;
    }
}
