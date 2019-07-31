<?php

namespace Malek83\PolishVatPayer;

use Malek83\PolishVatPayer\Cache\CacheDecorator;
use Malek83\PolishVatPayer\Client\ClientInterface;
use Malek83\PolishVatPayer\Exception\PolishVatPayerConnectionException;
use Malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Library using Polish Ministry of Finance WebService to validate
 * if given VAT Number is registered as VAT Tax Payer in Poland
 *
 * Class PolishVatPayer
 * @package Malek83\PolishVatPayer
 */
class PolishVatPayer
{
    /**
     * @var string
     */
    protected const CACHE_KEY_PREFIX = 'pl.malek83.polishvatpayer.';

    /**
     * @var ClientInterface
     */
    protected $client = null;

    /**
     * @var CacheDecorator
     */
    protected $cache = null;

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * PolishVatPayer constructor.
     *
     * @param ClientInterface $client Client object that provides VAT Numbers web service
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, CacheDecorator $cache, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * check if company with given vat number is polish vat payer
     *
     * @param string $vatNumber
     * @return PolishVatNumberVerificationResult
     * @throws PolishVatPayerConnectionException when there is a problem with the connection
     *
     */
    protected function validateInternal(string $vatNumber): PolishVatNumberVerificationResult
    {
        $sanitizedVatNumber = static::sanitizeVatNumber($vatNumber);

        $cacheKey = $this->getCacheKey($sanitizedVatNumber);
        if ($this->cache->has($cacheKey) === false) {
            $this->logger->info(
                sprintf("VAT Number %s not found in cache.  Fetching VAT Number information from client.", $vatNumber)
            );
            $result = $this->client->verify($sanitizedVatNumber);
            $this->cache->set($cacheKey, $result, $this->cache->getTtl());

            return $result;
        }

        $this->logger->info(
            sprintf("VAT Number %s found in cache. Returning cache data.", $vatNumber)
        );
        return $this->cache->get($cacheKey);
    }

    /**
     * check if company with given VAT Number is valid Vat payer in Poland and simply returns boolean as the result
     *
     * @param string $vatNumber Vat Number to be checked for VAT Tax Registration
     * @return bool result of verification
     * @throws PolishVatPayerConnectionException when there is a problem with the connection
     *
     */
    public function isValid(string $vatNumber): bool
    {
        $result = $this->validateInternal($vatNumber);

        return $result->isValid();
    }

    /**
     * check if company with given VAT Number is valid Vat payer in Poland and simply returns full result
     *
     * @param string $vatNumber Vat Number to be checked for VAT Tax Registration
     * @return PolishVatNumberVerificationResult result of the verification
     * @throws PolishVatPayerConnectionException when there is a problem with the connection
     *
     */
    public function validate(string $vatNumber): PolishVatNumberVerificationResult
    {
        return $this->validateInternal($vatNumber);
    }

    /**
     * Sanitize given VAT Number to the requirements of API
     *
     * @param $vatNumber
     * @return string sanitized Vat Number
     */
    protected static function sanitizeVatNumber(string $vatNumber): string
    {
        return preg_replace('/[^0-9]/', '', $vatNumber);
    }

    /**
     * @param string $sanitizedVatNumber
     * @return string
     */
    protected function getCacheKey(string $sanitizedVatNumber): string
    {
        return self::CACHE_KEY_PREFIX.\sha1($sanitizedVatNumber);
    }
}
