<?php

namespace Malek83\PolishVatPayer\Test\unit\Builder;

use Malek83\PolishVatPayer\Builder\PolishVatPayerBuilder;
use Malek83\PolishVatPayer\PolishVatPayer;
use PHPUnit\Framework\TestCase;

/**
 * Class PolishVatPayerBuilderTest
 * @package Malek83\PolishVatPayer\Test\unit\Builder
 */
class PolishVatPayerBuilderTest extends TestCase
{
    /**
     * @return void
     */
    public function testBuildingWithDefaultDependencies(): void
    {
        $this->assertInstanceOf(PolishVatPayer::class, PolishVatPayerBuilder::builder()->build());
    }
}
