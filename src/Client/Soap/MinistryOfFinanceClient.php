<?php

namespace Malek83\PolishVatPayer\Client\Soap;

use Malek83\PolishVatPayer\Client\ClientInterface;
use Malek83\PolishVatPayer\Client\Soap\Response\CheckVATNumberResponse;
use Malek83\PolishVatPayer\Exception\PolishVatPayerConnectionException;
use Malek83\PolishVatPayer\Logger\LoggerAwareInterface;
use Malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use SoapFault;
use SoapClient;

/**
 * Client for Ministry of Finance Soap WebService
 *
 * Class MinistryOfFinanceClient
 * @package Malek83\PolishVatPayer\Client\Soap
 */
class MinistryOfFinanceClient implements ClientInterface
{
    /**
     * @var string
     */
    public const RESPONSE_IS_VAT_PAYER = 'C';

    /**
     * @var string
     */
    public const RESPONSE_IS_NOT_REGISTERED_AS_VAT_PAYER = 'N';

    /**
     * @var string
     */
    public const RESPONSE_IS_NOT_VAT_PAYER = 'Z';

    /**
     * @var string
     */
    public const RESPONSE_VAT_NUMBER_INVALID = 'I';

    /**
     * URL to WSDL
     *
     * @var string
     */
    protected $wsdl = 'https://sprawdz-status-vat.mf.gov.pl/?wsdl';

    /**
     * SOAP client
     *
     * @var SoapClient;
     */
    protected $soapClient = null;

    /**
     * @var array classMap used to map WSDL types to PHP classes
     */
    protected $classMap = [
        'TWynikWeryfikacjiVAT' => CheckVATNumberResponse::class
    ];

    /**.
     * @param string $wsdl [optional] Custom URL to WSDL
     * @param array $classMap [optional] Custom Class Map for SoapClient
     * @param SoapClient [optional] $soapClient Custom SoapClient Object
     */
    public function __construct(string $wsdl = null, array $classMap = null, SoapClient $soapClient = null)
    {
        if ($wsdl !== null) {
            $this->wsdl = $wsdl;
        }

        if ($classMap !== null) {
            $this->classMap = $classMap;
        }

        if ($soapClient !== null) {
            $this->soapClient = $soapClient;
        }
    }

    /*
     * {@inheritDoc}
     */
    public function verify(string $vatNumber)
    {
        try {
            $response = $this->getSoapClient()->SprawdzNIP($vatNumber);
            return $this->prepareResult($vatNumber, $response);
        } catch (SoapFault $exception) {
            throw new PolishVatPayerConnectionException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * Prepares the result of verification
     *
     * @param string $vatNumber given VAT Number
     * @param CheckVATNumberResponse $response result received from web service
     * @return PolishVatNumberVerificationResult result of VAT Number verification
     * @throws PolishVatPayerConnectionException
     */
    protected function prepareResult($vatNumber, CheckVATNumberResponse $response)
    {
        switch ($response->getCode()) {
            case static::RESPONSE_IS_VAT_PAYER:
                $isValid = true;
                break;
            case static::RESPONSE_IS_NOT_REGISTERED_AS_VAT_PAYER:
            case static::RESPONSE_VAT_NUMBER_INVALID:
            case static::RESPONSE_IS_NOT_VAT_PAYER:
                $isValid = false;
                break;
            default:
                throw new PolishVatPayerConnectionException();
        }

        return new PolishVatNumberVerificationResult($vatNumber, $isValid, $response->getMessage());
    }

    /**
     * Returns and instantiate if necessary the SoapClient object.
     *
     * @return \SoapClient
     */
    protected function getSoapClient(): SoapClient
    {
        if ($this->soapClient === null) {
            $this->soapClient = new SoapClient($this->wsdl, [
                'classmap' => $this->classMap
            ]);
        }
        return $this->soapClient;
    }
}
