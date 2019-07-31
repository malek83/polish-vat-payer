<?php

namespace Malek83\PolishVatPayer\Cache;

use Malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use Psr\SimpleCache\CacheInterface;
use DateInterval;

/**
 * Class CacheDecorator
 * @package Malek83\PolishVatPayer\Cache
 */
final class CacheDecorator
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var DateInterval
     */
    private $ttl;

    /**
     * CacheDecorator constructor.
     * @param CacheInterface $cache Cache object compatible with PSR-16
     * @param DateInterval $ttl
     */
    public function __construct(CacheInterface $cache, DateInterval $ttl)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * @return DateInterval
     */
    public function getTtl(): DateInterval
    {
        return $this->ttl;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * @param string $key The key of the item to store.
     * @param PolishVatNumberVerificationResult $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set(string $key, PolishVatNumberVerificationResult $value, $ttl = null): bool
    {
        return $this->cache->set($key, $value, $ttl);
    }


    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param PolishVatNumberVerificationResult|null $default Default value to return if the key does not exist.
     *
     * @return PolishVatNumberVerificationResult|null The value of the item from the cache,
     * or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(
        string $key,
        PolishVatNumberVerificationResult $default = null
    ): ?PolishVatNumberVerificationResult {
        return $this->cache->get($key, $default);
    }
}
