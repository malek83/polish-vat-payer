<?php

namespace Malek83\PolishVatPayer\Result;

/**
 * Verification Result object
 *
 * Class PolishVatNumberVerificationResult
 * @package Malek83\PolishVatPayer\Result
 */
class PolishVatNumberVerificationResult
{

    /**
     * @var string VAT Number
     */
    protected $vatNumber;

    /**
     * @var boolean Result of validation
     */
    protected $isValid;

    /**
     * @var string Human readable message
     */
    protected $message;

    /**
     * PolishVatNumberVerificationResult constructor.
     * @param string $vatNumber Verified VAT Number
     * @param boolean $isValid Result of verification
     * @param string $message human readable message
     */
    public function __construct(string $vatNumber, bool $isValid, string $message)
    {
        $this->vatNumber = $vatNumber;
        $this->isValid = $isValid;
        $this->message = $message;
    }

    /**
     * @return string Verified VAT Number
     */
    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    /**
     * @return bool Result of verification if given VAT Number is registered as Polish VAT Payer
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @return string Human readable message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
