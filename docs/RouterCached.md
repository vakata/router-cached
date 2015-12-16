# vakata\routerCached\RouterCached
A caching router (extends the \vakata\router\Router class).

## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\routercached\routercached__construct)|Create an instance.|

---



### vakata\routerCached\RouterCached::__construct
Create an instance.  


```php
public function __construct (  
    \CacheInterface $cache,  
    integer $cacheTimeout,  
    callable|null $keyGenerator,  
    array|null $cachedVerbs,  
    string $cacheNamespace  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$cache` | `\CacheInterface` | the cache instance to use |
| `$cacheTimeout` | `integer` | the cache duration |
| `$keyGenerator` | `callable`, `null` | an optional callback so that you can return custom keys for each request |
| `$cachedVerbs` | `array`, `null` | which HTTP verbs to cache |
| `$cacheNamespace` | `string` | the cache namespace to use |

---

