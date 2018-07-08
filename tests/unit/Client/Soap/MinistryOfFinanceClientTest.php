<?php

namespace malek83\PolishVatPayer\Test\unit\Client\Soap;

use malek83\PolishVatPayer\Client\ClientInterface;
use malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;
use malek83\PolishVatPayer\Exception\PolishVatPayerConnectionException;
use malek83\PolishVatPayer\Client\Soap\Response\CheckVATNumberResponse;
use malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use PHPUnit\Framework\TestCase;
use SoapClient;

class MinistryOfFinanceClientTest extends TestCase
{

    public function testIsInstantiable()
    {
        $this->assertInstanceOf(ClientInterface::class, new MinistryOfFinanceClient());
    }

    public function testIsServiceUnavailable()
    {
        $this->expectException(PolishVatPayerConnectionException::class);

        $soapClientStub = $this->getMockBuilder(SoapClient::class)
            ->setMethods(['__construct', 'SprawdzNIP'])
            ->getMock();

        $soapClientStub
            ->method('SprawdzNIP')
            ->will($this->throwException(new \SoapFault('503', 'service unavailable')));

        $client = new MinistryOfFinanceClient(null, null, $soapClientStub);
        $client->verify('1234567890');
    }

    public function testNotRegisteredAsPolishVatPayer()
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

    public function testIsPolishVatPayer()
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

    public function testIsNotRegisteredAsPolishVatPayer()
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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareStubs(string $vatNumber, string $code, string $message)
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
            ->setMethods(['__construct', 'SprawdzNIP'])
            ->getMock();

        $soapClientStub
            ->method('SprawdzNIP')
            ->with($vatNumber)
            ->will($this->returnValue($responseStub));

        return $soapClientStub;
    }

}