<?php

namespace SlyDeath\NestedCaching;

use Blade;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

/**
 * Class NestedCachingServiceProvider
 *
 * @package SlyDeath\NestedCaching
 */
class NestedCachingServiceProvider extends ServiceProvider
{
    /**
     * Supported drivers
     *
     * @var array
     */
    protected $supported_drivers = [
        'redis',
        'memcached',
    ];
    
    /**
     * Bootstrap any application services
     *
     * @param Kernel $kernel
     *
     * @throws BadDriverException
     */
    public function boot(Kernel $kernel)
    {
        $this->checkCacheDriverSupport();
        $this->applyMiddleware($kernel);
        $this->applyBladeDirectives();
        $this->publishConfig();
    }
    
    /**
     * Checks cache driver for compatibility
     *
     * @throws BadDriverException
     */
    public function checkCacheDriverSupport()
    {
        if ( ! in_array(config('cache.default'), $this->supported_drivers, true)) {
            throw new BadDriverException(
                'Your cache driver does not supported.
                Supported drivers: ' . implode(', ', $this->supported_drivers)
            );
        }
    }
    
    /**
     * Installing middleware to entire clear the cache
     *
     * @param $kernel
     */
    public function applyMiddleware($kernel)
    {
        if (config('nested-caching.disabled') || in_array(app('env'), config('nested-caching.expelled-envs'), true)) {
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
        
        $this->app->singleton(CacheStack::class);
        $this->app->singleton(BladeDirectives::class);
    }
}
