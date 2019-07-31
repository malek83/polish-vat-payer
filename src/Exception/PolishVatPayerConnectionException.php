<?php

namespace Malek83\PolishVatPayer\Exception;

use Exception;

/**
 * Exception is thrown if there is a problem with the connection with API
 *
 * @param string $message [optional] The Exception message to throw.
 * @param int $code [optional] The Exception code.
 * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
 * @package Malek83\PolishVatPayer\Exception
 */
class PolishVatPayerConnectionException extends Exception implements PolishVatPayerExceptionInterface
{
    /**
     * @var string
     */
    protected $message = "This service is currently unable to handle the request.";

    /**
     * @var int
     */
    protected $code = 503;
}
