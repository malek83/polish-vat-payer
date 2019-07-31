<?php

namespace Malek83\PolishVatPayer\Test\unit;

use Malek83\PolishVatPayer\Builder\PolishVatPayerBuilder;
use Malek83\PolishVatPayer\Client\ClientInterface;
use Malek83\PolishVatPayer\Factory\PolishVatPayerFactory;
use Malek83\PolishVatPayer\PolishVatPayer;
use Malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;
use PHPUnit\Framework\TestCase;
use Desarrolla2\Cache\Memory;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Unit tests for class PolishVatPayer
 *
 * Class PolishVatPayerTest
 * @package Malek83\PolishVatPayer\Test\unit
 */
class PolishVatPayerTest extends TestCase
{
    /**
     * @var string
     */
    protected const MESSAGE_PAYER
        = 'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT czynny';

    /**
     * @var stringprivate
     */
    protected const MESSAGE_RELEASED
        = 'Podmiot o podanym identyfikatorze podatkowym NIP jest zarejestrowany jako podatnik VAT zwolniony';

    /**
     * @var string
     */
    protected const MESSAGE_NOT_REGISTERED
        = 'Podmiot o podanym identyfikatorze podatkowym NIP nie jest zarejestrowany jako podatnik VAT';

    /**
     * @return void
     */
    public function testIsInstantiable(): void
    {
        $clientStub = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $this->assertInstanceOf(PolishVatPayer::class, $validator);
    }

    /**
     * @return void
     */
    public function testIsValidReturnsTrueForPolishVatPayer(): void
    {
        $clientStub = $this->prepareStubs(
            '8495468971',
            true,
            self::MESSAGE_PAYER
        );

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $this->assertTrue($validator->isValid('8495468971'));
    }

    /**
     * @return void
     */
    public function testIsValidReturnsFalseForExemptedFromVatTax(): void
    {

        $clientStub = $this->prepareStubs(
            '3588862712',
            false,
            self::MESSAGE_RELEASED
        );

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $this->assertFalse($validator->isValid('3588862712'));
    }

    /**
     * @return void
     */
    public function testIsValidReturnsFalseForNotRegisteredAsPolishVatPayer(): void
    {
        $clientStub = $this->prepareStubs(
            '2472157980',
            false,
            self::MESSAGE_NOT_REGISTERED
        );

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $this->assertFalse($validator->isValid('2472157980'));
    }

    /**
     * @return void
     */
    public function testValidateReturnsTrueForPolishVatPayer(): void
    {

        $clientStub = $this->prepareStubs(
            '8495468971',
            true,
            self::MESSAGE_PAYER
        );


        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $result = $validator->validate('8495468971');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertTrue($result->isValid());
    }

    /**
     * @return void
     */
    public function testValidateReturnsFalseForExemptedFromVatTax(): void
    {
        $clientStub = $this->prepareStubs(
            '3588862712',
            false,
            self::MESSAGE_RELEASED
        );

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $result = $validator->validate('3588862712');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertFalse($result->isValid());
    }

    /**
     * @return void
     */
    public function testValidateReturnsFalseForNotRegisteredAsPolishVatPayer(): void
    {
        $clientStub = $this->prepareStubs(
            '2472157980',
            false,
            'Podmiot o podanym identyfikatorze podatkowym NIP nie jest zarejestrowany jako podatnik VAT'
        );

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();


        $result = $validator->validate('2472157980');

        $this->assertInstanceOf(PolishVatNumberVerificationResult::class, $result);
        $this->assertFalse($result->isValid());
    }

    public function testGetResponseFromCache(): void
    {
        $clientStub = $this->prepareStubs(
            '8495468971',
            true,
            self::MESSAGE_PAYER
        );
        $clientStub->expects($this->once())->method('verify');

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->setCache(new Memory())
            ->build();

        $this->assertTrue($validator->isValid('8495468971'));
        $this->assertTrue($validator->isValid('8495468971'));
    }

    public function testGetResponseWithoutCache(): void
    {
        $clientStub = $this->prepareStubs(
            '8495468971',
            true,
            self::MESSAGE_PAYER
        );
        $clientStub->expects($this->exactly(2))->method('verify');

        $validator = PolishVatPayerBuilder::builder()
            ->setClient($clientStub)
            ->build();

        $this->assertTrue($validator->isValid('8495468971'));
        $this->assertTrue($validator->isValid('8495468971'));
    }

    /**
     * Prepare stubs for tests
     *
     * @param string $vatNumber
     * @param bool $verificationResult
     * @param string $message
     * @return ClientInterface
     */
    protected function prepareStubs($vatNumber, $verificationResult, $message): PHPUnit_Framework_MockObject_MockObject
    {
        $clientStub = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['verify'])
            ->getMock();

        $responseStub = new PolishVatNumberVerificationResult($vatNumber, $verificationResult, $message);

        $clientStub->method('verify')
            ->with($this->equalTo($vatNumber))
            ->will($this->returnValue($responseStub));

        return $clientStub;
    }
}
