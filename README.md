[![Build Status](https://travis-ci.org/malek83/polish-vat-payer.svg?branch=master)](https://travis-ci.org/malek83/polish-vat-payer)

# Polish VAT Payer

Simple library using Polish Ministry of Finance WebService to validate
if given VAT Number is registered as VAT Tax Payer in Poland.

## Contents

* [Installation](#installation)
* [Usage](#usage)
* [Roadmap](#roadmap)
* [Changelog](#changelog)
* [License](#license)

## Installation

This library is available on [Packagist](http://packagist.org/packages/malek83/polish-vat-payer):

```bash
$ composer require malek83/polish-vat-payer
```


## Usage

There are two easy ways to use this library:

### to get the boolean result of verification

```php
use Malek83\PolishVatPayer\Builder\PolishVatPayerBuilder;
use Malek83\PolishVatPayer\PolishVatPayer;

/** @var PolishVatPayer $validator */
$validator = PolishVatPayerBuilder::builder()->build();

$bool = $validator->isValid('1568255600'); //returns boolean
```

### to get the Full Response Object, containing all needed information


```php
use Malek83\PolishVatPayer\Builder\PolishVatPayerBuilder;
use Malek83\PolishVatPayer\PolishVatPayer;

/** @var PolishVatPayer $validator */
$validator = PolishVatPayerBuilder::builder()->build();

/** @var \Malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult $result */
$result = $validator->validate('1234567890');

var_dump($result->isValid()); // gives boolean, true if company is VAT Payer, otherwise false
var_dump($result->getVatNumber()); //gives string, the verificated VAT number
var_dump($result->getMessage()); //gives string, the human readable message
```

### Request cache

Request cache can be used if necessary. Any PSR-16 Compatible component can be used (i.e. symfony/cache)

By default cache is turned off. To use cache call the setter method during building the facade

```php
use Malek83\PolishVatPayer\Builder\PolishVatPayerBuilder;
use Malek83\PolishVatPayer\PolishVatPayer;

/** @var PolishVatPayer $validator */
$validator = PolishVatPayerBuilder::builder()
    ->setCache(new AnyPsr16CompatibleCache())
    ->setTtl(new DateInterval('PT1H'))
    ->build();
```

Default TTL is 1 hour. It can be also overriden while during the build process.

### Request Log

Request log can be defined also. It's PSR-3 Compatible (i.e. Monolog)

By default it's turned off, to turn it on simply call ```setLogger``` while building:

```php
use Malek83\PolishVatPayer\Builder\PolishVatPayerBuilder;
use Malek83\PolishVatPayer\PolishVatPayer;

/** @var PolishVatPayer $validator */
$validator = PolishVatPayerBuilder::builder()
    ->setLogger(new AnyPsr3CompatibleLogger())
    ->build();
```

## Roadmap

All future features are listed in [Roadmap GitHub project](https://github.com/malek83/polish-vat-payer/projects/1).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) file for more information what has changed recently.

## Local environment for development & testing

```bash
$ docker build -t polish-vat-payer ./docker/

$ docker run -d --name polish-vat-payer --volume ${PWD}:/source polish-vat-payer
```

## License

The MIT License (MIT). Please see [License](LICENSE) for more information.