<?php

namespace Malek83\PolishVatPayer\Test\unit\Client\Soap;

use Malek83\PolishVatPayer\Client\ClientInterface;
use Malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;
use Malek83\PolishVatPayer\Exception\PolishVatPayerConnectionException;
use Malek83\PolishVatPayer\Client\Soap\Response\CheckVATNumberResponse;
use Malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use PHPUnit\Framework\TestCase;
use SoapClient;

/**
 * Unit tests for class MinistryOfFinanceClient
 *
 * Class MinistryOfFinanceClientTest
 * @package Malek83\PolishVatPayer\Test\unit\Client\Soap
 *
 */
class MinistryOfFinanceClientTest extends TestCase
{
    /**
     * @return void
     */
    public function testIsInstantiable(): void
    {
        $this->assertInstanceOf(ClientInterface::class, new MinistryOfFinanceClient());
    }

    /**
     * @return void
     */
    public function testIsServiceUnavailable(): void
    {
        $this->setExpectedException(PolishVatPayerConnectionException::class);

        $soapClientStub = $this->getMockBuilder(SoapClient::class)
            ->setConstructorArgs([""])
            ->setMethods(['__construct', 'SprawdzNIP'])
            ->getMock();

        $soapClientStub
            ->method('SprawdzNIP')
            ->will($this->throwException(new \SoapFault('503', 'service unavailable')));

        $client = new MinistryOfFinanceClient(null, null, $soapClientStub);
        $client->verify('1234567890');
    }

    /**
     * @return void
     */
    public function testNotRegisteredAsPolishVatPayer(): void
    {
        $soapClientStub = $this->prepareStubs(
            '4920824345',
            MinistryOfFinanceClient::RESPONSE_IS_NOT_REGISTERED_AS_VAT_PAYER,
            'Podmiot o podanym identyfikatorze podatkowym NIP nie jest zarejestrowany jako podatnik VAT'
        );

        $client = new MinistryOfFinanceClient(null, null, $soapClientStub);
        $result = $client->verify('4920824345');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertFalse($result->isValid());
    }

    /**
     * @return void
     */
    public function testIsPolishVatPayer(): void
    {
        $soapClientStub = $this->prepareStubs(
            '6820792598',
            MinistryOfFinanceClient::RESPONSE_IS_VAT_PAYER,
            'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT czynny'
        );

        $client = new MinistryOfFinanceClient(null, null, $soapClientStub);
        $result = $client->verify('6820792598');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    /**
     * @return void
     */
    public function testIsNotRegisteredAsPolishVatPayer(): void
    {
        $soapClientStub = $this->prepareStubs(
            '4994162136',
            MinistryOfFinanceClient::RESPONSE_IS_NOT_VAT_PAYER,
            'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT zwolniony'
        );

        $client = new MinistryOfFinanceClient(null, null, $soapClientStub);
        $result = $client->verify('4994162136');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertFalse($result->isValid());
    }

    /**
     * Prepare stubs for tests
     *
     * @param string $vatNumber
     * @param string $code
     * @param string $message
     * @return SoapClient
     */
    protected function prepareStubs(string $vatNumber, string $code, string $message): SoapClient
    {
        $responseStub = $this->getMockBuilder(CheckVATNumberResponse::class)
            ->setConstructorArgs([$code, $message])
            ->setMethods(['getCode', 'getMessage'])
            ->getMock();
        $responseStub->method('getCode')
            ->willReturn($code);
        $responseStub->method('getMessage')
            ->willReturn($message);

        $soapClientStub = $this->getMockBuilder(SoapClient::class)
            ->setConstructorArgs([""])
            ->setMethods(['__construct', 'SprawdzNIP'])
            ->getMock();

        $soapClientStub
            ->method('SprawdzNIP')
            ->with($vatNumber)
            ->will($this->returnValue($responseStub));

        return $soapClientStub;
    }
}
