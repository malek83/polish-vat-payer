<?php

namespace malek83\PolishVatPayer;

use malek83\PolishVatPayer\Client\ClientInterface;
use malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;
use malek83\PolishVatPayer\Exception\PolishVatPayerConnectionException;
use malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;

/**
 * Library using Polish Ministry of Finance WebService to validate
 * if given VAT Number is registered as VAT Tax Payer in Poland
 *
 * Class PolishVatPayer
 * @package malek83\PolishVatPayer
 */
class PolishVatPayer
{
    /**
     * @var ClientInterface
     */
    protected $client = null;

    /**
     * PolishVatPayer constructor.
     *
     * @param ClientInterface $client Client object that provides VAT Numbers web service
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * check if company with given vat number is polish vat payer
     *
     * @param string $vatNumber
     * @throws PolishVatPayerConnectionException when there is a problem with the connection
     *
     * @return PolishVatNumberVerificationResult
     */
    protected function validateInternal($vatNumber)
    {
        /** @var PolishVatNumberVerificationResult $response */
        $response = $this->client->verify(static::sanitizeVatNumber($vatNumber));

        return $response;
    }

    /**
     * check if company with given VAT Number is valid Vat payer in Poland and simply returns boolean as the result
     *
     * @param string $vatNumber Vat Number to be checked for VAT Tax Registration
     * @throws PolishVatPayerConnectionException when there is a problem with the connection
     *
     * @return bool result of verification
     */
    public function isValid($vatNumber)
    {
        $result = $this->validateInternal($vatNumber);

        return $result->isValid();
    }

    /**
     * check if company with given VAT Number is valid Vat payer in Poland and simply returns full result
     *
     * @param string $vatNumber Vat Number to be checked for VAT Tax Registration
     * @throws PolishVatPayerConnectionException when there is a problem with the connection
     *
     * @return PolishVatNumberVerificationResult result of the verification
     */
    public function validate($vatNumber)
    {
        return $this->validateInternal($vatNumber);
    }

    /**
     * Sanitize given VAT Number to the requirements of API
     *
     * @param $vatNumber
     * @return string sanitized Vat Number
     */
    protected static function sanitizeVatNumber($vatNumber)
    {
        return preg_replace('/[^0-9]/', '', $vatNumber);
    }

    /**
     * Returns Provider of Vat Number Verification web service object
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}
