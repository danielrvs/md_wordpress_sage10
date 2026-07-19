<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Cache\Store;

class WordPressCacheStore implements Store
{
    protected string $prefix;
    protected string $group = 'acorn';

    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function get($key)
    {
        $value = wp_cache_get($this->prefix . $key, $this->group);
        return $value === false ? null : $value;
    }

    public function many(array $keys): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        return $results;
    }

    public function put($key, $value, $seconds): bool
    {
        return wp_cache_set($this->prefix . $key, $value, $this->group, (int) $seconds);
    }

    public function touch($key, $seconds): bool
    {
        $value = $this->get($key);
        if ($value !== null) {
            return $this->put($key, $value, $seconds);
        }
        return false;
    }

    public function putMany(array $values, $seconds): bool
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $seconds);
        }
        return true;
    }

    public function increment($key, $value = 1)
    {
        return wp_cache_incr($this->prefix . $key, $value, $this->group);
    }

    public function decrement($key, $value = 1)
    {
        return wp_cache_decr($this->prefix . $key, $value, $this->group);
    }

    public function forever($key, $value): bool
    {
        return $this->put($key, $value, 0);
    }

    public function forget($key): bool
    {
        return wp_cache_delete($this->prefix . $key, $this->group);
    }

    public function flush(): bool
    {
        return wp_cache_flush();
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
