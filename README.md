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

### to get the boolean result of verication

```php
use malek83\PolishVatPayer\PolishVatPayer;

$validator = new PolishVatPayer();
$bool = $validator->isValid('1568255600'); //returns boolean
```

### to get the Full Response Object, containg all needed information


```php
use malek83\PolishVatPayer\PolishVatPayer;

$validator = new PolishVatPayer();

/** @var \malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult $result */
$result = $PolishVatPayer->validate('1234567890');

var_dump($result->isValid()); // gives boolean, true if company is VAT Payer, otherwise false
var_dump($result->getVatNumber()); //gives string, the verificated VAT number
var_dump($result->getMessage()); //gives string, the human readable message
```

## Roadmap

All future features are listed in [Roadmap GitHub project](https://github.com/malek83/polish-vat-payer/projects/1).

## Changelog

Please see [CHANGELOG](CHANGELOG.md) file for more information what has changed recently.

## License

The MIT License (MIT). Please see [License](LICENSE) for more information.