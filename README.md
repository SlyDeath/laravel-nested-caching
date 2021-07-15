# Nested Caching for Laravel with caches stack

[![Latest Stable Version](https://poser.pugx.org/slydeath/laravel-nested-caching/v/stable)](https://packagist.org/packages/slydeath/laravel-nested-caching)
[![Total Downloads](https://poser.pugx.org/slydeath/laravel-nested-caching/downloads)](https://packagist.org/packages/slydeath/laravel-nested-caching)
[![License](https://poser.pugx.org/slydeath/laravel-nested-caching/license)](https://packagist.org/packages/slydeath/laravel-nested-caching)

## Minimum requirements

- PHP 7.4
- Laravel 8

## Installation

Add package to composer.json:

```bash
composer require slydeath/laravel-nested-caching
```

Open `config/app.php` and add the service provider to the array `providers`:

```php
SlyDeath\NestedCaching\NestedCachingServiceProvider::class,
```

To place the configuration file, run:

```bash
php artisan vendor:publish --provider="SlyDeath\NestedCaching\NestedCachingServiceProvider" --tag=config
```

## How to use?

### Caching any HTML chunk

To cache any HTML chunk, you just need to pass the caching key to the `@cache` directive fragment:

```html
@cache('simple-cache')
    <div>
        This is an arbitrary piece of HTML that will be cached 
        using the «simple-cache» key
    </div>
@endCache
```

### Model caching

To enable model caching support, add the trait to it `NestedCacheable`:

```php
use SlyDeath\NestedCaching\NestedCacheable;

class User extends Model
{
    use NestedCacheable;
}
```

In the template, to cache a model, you need to pass its instance to the `@cache` directive:

```html
@cache($user)
<div>App\User model caching:</div>
<ul>
    <li>Name: {{ $user->name }}</li>
    <li>Email: {{ $user->email }}</li>
</ul>
@endCache
```

### Caching the model for a specified time

To cache the model for a certain time, specify the lifetime in minutes as the second parameter:

```html
@cache($user, 1440)
    <div>...</div>
@endCache
 ```

#### Updating the «parent»

For update the cache of the «parent model», we need setup touches:

```php
use SlyDeath\NestedCaching\NestedCacheable;

class CarUser extends Model
{
    use NestedCacheable;

    // Specifying the parent relations
    protected $touches = ['user']; 

    // Parent relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Usage example:

**resources/views/user.blade.php**

```html
@cache($user)
<section>
    <h2>User's cars {{ $user->name }}</h2>
    <ul>
        @foreach($user->cars as $car)
        @include('user-car');
        @endforeach
    </ul>
</section>
@endCache
```

**resources/views/user-car.blade.php**

```html
@cache($car)
    <li>{{ $car->brand }}</li>
@endCache
```

### Collection caching

Example of caching a collection:

```html
@cache($users)
    @foreach ($users as $user)
        @include('user');
    @endforeach
@endCache
```

### How to remove stack cache?

Just run this code at bottom of your page:

```php
app(SlyDeath\NestedCaching\CacheStack::class)->clearCache();
```

## Enable PhpStorm support

Go to the `Settings → PHP → Blade`, then uncheck **Use default settings**. Go to **Directives** tab and press «+» to add
another one custom directive:

- Name: `cache`
- Checkbox **Has parameters** → `true`
- Prefix: ```<?php if ( ! app('SlyDeath\NestedCaching\BladeDirectives')->cache(```
- Suffix: ```)) { ?>```

And add close directive without parameters:

- Name: `endcache` or `endCache`, whatever you use