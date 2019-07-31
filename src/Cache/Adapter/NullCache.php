<?php

namespace Malek83\PolishVatPayer\Cache\Adapter;

use Psr\SimpleCache\CacheInterface;

/**
 * Class NullCache
 * @package Malek83\PolishVatPayer\Cache\Adapter
 */
class NullCache implements CacheInterface
{
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|int|null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return true;
    }

    /**
     * @param iterable $keys
     * @param mixed|null $default
     * @return iterable
     */
    public function getMultiple($keys, $default = null)
    {
        return [];
    }

    /**
     * @param iterable $values
     * @param \DateInterval|int|null $ttl
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        return true;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return false;
    }
}
