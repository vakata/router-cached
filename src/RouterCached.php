<?php

namespace vakata\routerCached;

use \vakata\router\Router;
use \vakata\cache\CacheInterface;

class RouterCached extends Router
{
    protected $cache = null;
    protected $cacheTimeout = null;
    protected $cacheNamespace = null;
    protected $keyGenerator = null;
    protected $cachedVerbs = null;

    public function __construct(
        CacheInterface $cache,
        $cacheTimeout = 1440,
        callable $keyGenerator = null,
        array $cachedVerbs = null,
        $cacheNamespace = ''
    ) {
        if ($cachedVerbs === null) {
            $cachedVerbs = [ 'GET', 'HEAD', 'OPTIONS' ];
        }
        if ($keyGenerator === null) {
            $keyGenerator = function ($route, $verb) {
                return md5($verb . ' ' . $route);
            };
        }

        $this->cache = $cache;
        $this->cacheTimeout = $cacheTimeout;
        $this->keyGenerator = $keyGenerator;
        $this->cachedVerbs = $cachedVerbs;
        $this->cacheNamespace = $cacheNamespace;
    }

    public function run($request, $verb = 'GET')
    {
        if (!in_array($verb, $this->cachedVerbs)) {
            return parent::run($request, $verb);
        }
        $key = call_user_func($this->keyGenerator, $request, $verb);

        try {
            $cached = $this->cache->get($key, $this->cacheNamespace);
            http_response_code($cached['code']);
            foreach ($cached['head'] as $head) {
                header($head);
            }
            echo $cached['body'];
            return $cached['rtrn'];
        }
        catch (\Exception $e) {
            ob_start();
            $rtrn = parent::run($request, $verb);
            $body = ob_get_contents();
            $head = headers_list();
            $code = http_response_code();
            ob_end_clean();

            if (!$code || $code === 200) {
                $this->cache->set(
                    $key,
                    [ 'rtrn' => $rtrn, 'head' => $head, 'body' => $body, 'code' => $code ],
                    $this->cacheNamespace,
                    $this->cacheTimeout
                );
            }

            echo $body;
            return $rtrn;
        }
    }
}
