<?php

namespace SlyDeath\NestedCaching;

use Illuminate\Cache\Events\KeyWritten;

/**
 * Class CacheWrittenListener
 *
 * @package SlyDeath\NestedCaching
 */
class CacheWrittenListener
{
    /**
     * Handle the event
     *
     * @param KeyWritten $event
     *
     * @return void
     */
    public function handle(KeyWritten $event)
    {
        if (config('nested-caching.another-caching')) {
            app(CacheStack::class)->addAnotherCache([
                'tags' => $event->tags,
                'key'  => $event->key,
            ]);
        }
    }
}
