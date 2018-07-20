<?php

namespace malek83\PolishVatPayer\Test\unit\Factory;

use malek83\PolishVatPayer\Client\ClientInterface;
use malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;
use malek83\PolishVatPayer\Factory\PolishVatPayerFactory;
use malek83\PolishVatPayer\PolishVatPayer;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class PolishVatPayerFactory
 *
 * Class PolishVatPayerFactoryTest
 * @package malek83\PolishVatPayer\Test\unit\Factory
 */
class PolishVatPayerFactoryTest extends TestCase
{
    public function testReturnValidPolishVatPayerObjectWithDefaultClientObject()
    {
        $instance = PolishVatPayerFactory::create();

        $this->assertInstanceOf(PolishVatPayer::class, $instance);
        $this->assertInstanceOf(MinistryOfFinanceClient::class, $instance->getClient());
    }

    public function testReturnValidPolishVatPayerObjectWithCustomClientObject()
    {
        $clientStub = $this
            ->getMockBuilder(ClientInterface::class)
            ->getMock();

        $instance = PolishVatPayerFactory::create($clientStub);

        $this->assertInstanceOf(PolishVatPayer::class, $instance);
        $this->assertInstanceOf(get_class($clientStub), $instance->getClient());
    }
}
