<?php

namespace malek83\PolishVatPayer\Test\unit;

use malek83\PolishVatPayer\Client\ClientInterface;
use malek83\PolishVatPayer\PolishVatPayer;
use malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class PolishVatPayer
 *
 * Class PolishVatPayerTest
 * @package malek83\PolishVatPayer\Test\unit
 */
class PolishVatPayerTest extends TestCase
{

    public function testIsInstantiable()
    {
        $clientStub = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $this->assertInstanceOf(PolishVatPayer::class, new PolishVatPayer($clientStub));
    }

    public function testIsValidReturnsTrueForPolishVatPayer()
    {

        $clientStub = $this->prepareStubs(
            '8495468971',
            true,
            'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT czynny'
        );


        $validator = new PolishVatPayer($clientStub);
        $this->assertTrue($validator->isValid('8495468971'));
    }

    public function testIsValidReturnsFalseForExemptedFromVatTax()
    {

        $clientStub = $this->prepareStubs(
            '3588862712',
            false,
            'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT zwolniony'
        );

        $validator = new PolishVatPayer($clientStub);
        $this->assertFalse($validator->isValid('3588862712'));
    }

    public function testIsValidReturnsFalseForNotRegisteredAsPolishVatPayer()
    {
        $clientStub = $this->prepareStubs(
            '2472157980',
            false,
            'Podmiot o podanym identyfikatorze podatkowym NIP nie jest zarejestrowany jako podatnik VAT'
        );

        $validator = new PolishVatPayer($clientStub);
        $this->assertFalse($validator->isValid('2472157980'));
    }

    public function testValidateReturnsTrueForPolishVatPayer()
    {

        $clientStub = $this->prepareStubs(
            '8495468971',
            true,
            'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT czynny'
        );


        $validator = new PolishVatPayer($clientStub);
        $result = $validator->validate('8495468971');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    public function testValidateReturnsFalseForExemptedFromVatTax()
    {

        $clientStub = $this->prepareStubs(
            '3588862712',
            false,
            'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT zwolniony'
        );

        $validator = new PolishVatPayer($clientStub);
        $result = $validator->validate('3588862712');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertFalse($result->isValid());
    }

    public function testValidateReturnsFalseForNotRegisteredAsPolishVatPayer()
    {
        $clientStub = $this->prepareStubs(
            '2472157980',
            false,
            'Podmiot o podanym identyfikatorze podatkowym NIP nie jest zarejestrowany jako podatnik VAT'
        );

        $validator = new PolishVatPayer($clientStub);

        $result = $validator->validate('2472157980');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertFalse($result->isValid());
    }

    /**
     * Prepare stubs for tests
     *
     * @param string $vatNumber
     * @param bool $verificationResult
     * @param string $message
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareStubs($vatNumber, $verificationResult, $message)
    {
        $responseStub = $this->getMockBuilder(PolishVatNumberVerificationResult::class)
            ->setConstructorArgs([
                $vatNumber,
                $verificationResult,
                $message
            ])
            ->setMethods(['isValid', 'getMessage', 'getVatNumber'])
            ->getMock();

        $responseStub
            ->method('isValid')
            ->willReturn($verificationResult);
        $responseStub
            ->method('getMessage')
            ->willReturn($message);
        $responseStub
            ->method('getVatNumber')
            ->willReturn($vatNumber);

        $clientStub = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['verify'])
            ->getMock();

        $clientStub->method('verify')
            ->with($this->equalTo($vatNumber))
            ->will($this->returnValue($responseStub));

        return $clientStub;
    }
}
