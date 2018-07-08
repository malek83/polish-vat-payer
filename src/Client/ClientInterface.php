<?php

namespace malek83\PolishVatPayer\Client;

use malek83\PolishVatPayer\Exception\PolishVatPayerException;
use malek83\PolishVatPayer\Result\PolishVatNumberVerificationResult;

interface ClientInterface
{
    /**
     * Veryfies at Ministry of Finances API if given VAT Number is registered as VAT Tax Payer in Poland
     *
     * @param string $vatNumber Polish VAT Number without leading country code
     * @return PolishVatNumberVerificationResult result of VAT Number verification
     * @throws PolishVatPayerException exception is thrown if something goes wrong
     */
    public function verify(string $vatNumber);
}