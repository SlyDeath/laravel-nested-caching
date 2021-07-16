<?php

namespace SlyDeath\NestedCaching\Providers;

use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SlyDeath\NestedCaching\CacheWrittenListener;

/**
 * Class EventServiceProvider
 *
 * @package SlyDeath\NestedCaching\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the package
     *
     * @var array
     */
    protected array $listen = [
        KeyWritten::class => [
            CacheWrittenListener::class,
        ],
    ];
    
    /**
     * The subscriber classes to register
     *
     * @var array
     */
    protected array $subscribe = [];
    
    /**
     * Get the events and handlers
     *
     * @return array
     */
    public function listens(): array
    {
        return $this->listen;
    }
    
    /**
     * Register the package's event listeners
     */
    public function boot()
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
        
        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }
}
