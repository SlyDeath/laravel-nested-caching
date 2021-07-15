<?php

namespace SlyDeath\NestedCaching;

use Cache;

/**
 * Class FlushCacheMiddleware
 *
 * @package SlyDeath\NestedCaching
 */
class FlushCacheMiddleware
{
    /**
     * Entire cache reset middleware
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
    {
        Cache::tags(config('nested-caching.cache-tag'))->flush();
        
        return $next($request);
    }
}