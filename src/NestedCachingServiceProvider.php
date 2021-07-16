<?php

namespace SlyDeath\NestedCaching;

use Blade;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use SlyDeath\NestedCaching\Providers\EventServiceProvider;

/**
 * Class NestedCachingServiceProvider
 *
 * @package SlyDeath\NestedCaching
 */
class NestedCachingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services
     *
     * @param Kernel $kernel
     */
    public function boot(Kernel $kernel)
    {
        $this->applyMiddleware($kernel);
        $this->applyBladeDirectives();
        $this->publishConfig();
    }
    
    /**
     * Installing middleware to entire clear the cache
     *
     * @param $kernel
     */
    public function applyMiddleware($kernel)
    {
        if ( ! config('nested-caching.disabled')) {
            return;
        }
        
        if (in_array(app('env'), config('nested-caching.expelled-envs'), true)) {
            $kernel->pushMiddleware('SlyDeath\NestedCaching\FlushCacheMiddleware');
        }
    }
    
    /**
     * Adding Blade directives
     */
    public function applyBladeDirectives()
    {
        Blade::directive('cache', function ($expression) {
            return "<?php if ( ! app('SlyDeath\NestedCaching\BladeDirectives')->cache({$expression}) ) { ?>";
        });
        
        Blade::directive('endcache', function () {
            return "<?php } echo app('SlyDeath\NestedCaching\BladeDirectives')->endCache(); ?>";
        });
        
        // Not the Laravel way, but for some beauty and flexible :)
        Blade::directive('endCache', function () {
            return "<?php } echo app('SlyDeath\NestedCaching\BladeDirectives')->endCache(); ?>";
        });
    }
    
    /**
     * Publishing configurations
     */
    public function publishConfig()
    {
        $config_path  = __DIR__ . '/../config/nested-caching.php';
        $publish_path = base_path('config/nested-caching.php');
        
        $this->publishes([$config_path => $publish_path], 'config');
    }
    
    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $config_path = __DIR__ . '/../config/nested-caching.php';
        $this->mergeConfigFrom($config_path, 'nested-caching');
        
        $this->app->register(EventServiceProvider::class);
        
        $this->app->singleton(CacheStack::class);
        $this->app->singleton(BladeDirectives::class);
    }
}
