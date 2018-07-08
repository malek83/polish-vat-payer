<?php
/**
 * Created by PhpStorm.
 * User: mmalecki2
 * Date: 29.06.2018
 * Time: 11:17
 */

namespace malek83\PolishVatPayer\Exception;

/**
 * Exception is thrown if there is a problem with the connection with API
 *
 * @param string $message [optional] The Exception message to throw.
 * @param int $code [optional] The Exception code.
 * @param Throwable $previous [optional] The previous throwable used for the exception chaining.
 * @package malek83\PolishVatPayer\Exception
 */
class PolishVatPayerConnectionException extends PolishVatPayerException
{
    protected $message = "This sevice is currently unable to handle the request.";
    protected $code = 503;
}