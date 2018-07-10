<?php

namespace malek83\PolishVatPayer\Client\Soap\Response;

/**
 * Response class to map WSDL type TWynikWeryfikacjiVAT to PHP class
 *
 * Class CheckVATNumberResponse
 * @package malek83\PolishVatPayer\Client\Soap\Response
 */
class CheckVATNumberResponse
{

    /**
     * @var string;
     */
    protected $Kod;

    /**
     * @var string
     */
    protected $Komunikat;

    public function __construct($code, $message)
    {
        $this->Kod = $code;
        $this->Komunikat = $message;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->Kod;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->Komunikat;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->Komunikat;
    }

}