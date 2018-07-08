<?php

namespace malek83\PolishVatPayer;

use malek83\PolishVatPayer\Client\ClientInterface;
use malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;
use malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;

class PolishVatPayer
{
    /**
     * @var ClientInterface
     */
    protected $client = null;

    /**
     * PolishVatPayer constructor.
     *
     * @param ClientInterface $client [optional] Custom client object that provides VAT Numbers web service
     */
    public function __construct(ClientInterface $client = null)
    {
        if ($client !== null) {
            $this->client = $client;
        }
    }

    /**
     * check if company with given vat number is polish vat payer
     *
     * @param string $vatNumber
     * @return PolishVatNumberVerificationResult
     */
    protected function validateInternal(string $vatNumber)
    {
        /** @var PolishVatNumberVerificationResult $response */
        $response = $this->getClientInstance()->verify(static::sanitizeVatNumber($vatNumber));

        return $response;
    }

    /**
     * checks if company with given VAT Number is valid Vat payer in Poland and simply returns boolean as the result
     *
     * @param string $vatNumber Vat Number to be checked for VAT Tax Registration
     * @return bool result of verification if
     */
    public function isValid(string $vatNumber)
    {
        $result = $this->validateInternal($vatNumber);

        return $result->isValid();
    }

    /**
     * checks if company with given VAT Number is valid Vat payer in Poland and simply returns full result
     *
     * @param string $vatNumber Vat Number to be checked for VAT Tax Registration
     * @return PolishVatNumberVerificationResult result of the verification
     */
    public function validate(string $vatNumber)
    {
        return $this->validateInternal($vatNumber);
    }

    /**
     * Returns and instantiate if necessary object of given class that implements ClientInterface interface.
     *
     * @return ClientInterface
     */
    protected function getClientInstance()
    {
        if ($this->client === null) {
            $this->client = new MinistryOfFinanceClient();
        }
        return $this->client;
    }

    /**
     * Sanitzes given VAT Number to the requirments of API
     *
     * @param $vatNumber
     * @return string sanitized Vat Number
     */
    protected static function sanitizeVatNumber($vatNumber)
    {
        return preg_replace('/[^0-9]/', '', $vatNumber);
    }
}