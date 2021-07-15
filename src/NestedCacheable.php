<?php

namespace SlyDeath\NestedCaching;

/**
 * Trait NestedCacheable
 *
 * @package SlyDeath\NestedCaching
 */
trait NestedCacheable
{
    /**
     * Create a unique caching key
     *
     * @return string
     */
    public function getNestedCacheKey(): string
    {
        // If there is no model id or updated_at field, then a key is generated from the content of the model...
        if ( ! $this->id || ! $this->updated_at) {
            return sha1($this);
        }
        
        // ...otherwise, the model key is compiled
        return get_class($this) . "::$this->id::{$this->updated_at->timestamp}";
    }
}