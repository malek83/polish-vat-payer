<?php

namespace Malek83\PolishVatPayer\Builder;

use Malek83\PolishVatPayer\Cache\Adapter\NullCache;
use Malek83\PolishVatPayer\Cache\CacheDecorator;
use Malek83\PolishVatPayer\Client\ClientInterface;
use Malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;
use Malek83\PolishVatPayer\Logger\LoggerAwareInterface;
use Malek83\PolishVatPayer\Logger\NullLogger;
use Malek83\PolishVatPayer\PolishVatPayer;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use DateInterval;

/**
 * instantiate PolishVatPayer object with given or default Client object
 *
 * Class PolishVatPayerBuilder
 * @package Malek83\PolishVatPayer\Builder
 */
final class PolishVatPayerBuilder
{
    /**
     * @var string default cache duration compatible with ISO 8601 duration specification.
     */
    private const DEFAULT_CACHE_DURATION = 'PT1H';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var DateInterval
     */
    private $ttl;

    /**
     * PolishVatPayerBuilder constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return PolishVatPayerBuilder
     */
    public static function builder(): self
    {
        return new self();
    }

    /**
     * @return PolishVatPayer
     */
    public function build(): PolishVatPayer
    {
        $logger = $this->getLogger();
        $client = $this->getClient();
        if ($client instanceof LoggerAwareInterface) {
            $client->setLogger($logger);
        }

        return new PolishVatPayer(
            $client,
            $this->getCacheDecorator(),
            $logger
        );
    }

    /**
     * @param CacheInterface $cache Cache object compatible with PSR-16
     */
    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return CacheInterface Cache object compatible with PSR-16
     */
    private function getCache(): CacheInterface
    {
        if ($this->cache === null) {
            $this->cache = new NullCache();
        }

        return $this->cache;
    }

    /**
     * @param DateInterval $ttl cache duration
     *
     * @return PolishVatPayerBuilder
     */
    public function setTtl(DateInterval $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @return DateInterval
     */
    private function getTtl(): DateInterval
    {
        if ($this->ttl === null) {
            $this->ttl = new DateInterval(self::DEFAULT_CACHE_DURATION);
        }

        return $this->ttl;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return PolishVatPayerBuilder
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param ClientInterface $client Custom client object that provides VAT Numbers web service
     */
    public function setClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return ClientInterface Client object that provides VAT Numbers web service
     */
    private function getClient(): ClientInterface
    {
        if ($this->client === null) {
            $this->client = new MinistryOfFinanceClient();
        }

        return $this->client;
    }

    /**
     * @return CacheDecorator
     */
    private function getCacheDecorator(): CacheDecorator
    {
        return new CacheDecorator($this->getCache(), $this->getTtl());
    }
}
