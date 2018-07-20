<?php

namespace malek83\PolishVatPayer\Factory;

use malek83\PolishVatPayer\Client\ClientInterface;
use malek83\PolishVatPayer\PolishVatPayer;
use malek83\PolishVatPayer\Client\Soap\MinistryOfFinanceClient;

class PolishVatPayerFactory
{

    /**
     * instantiate PolishVatPayer object with given or default Client object
     *
     * @param ClientInterface|null $client [optional] Custom client object that provides VAT Numbers web service
     * @return PolishVatPayer
     */
    public static function create(ClientInterface $client = null)
    {
        if ($client === null) {
            $client = new MinistryOfFinanceClient();
        }
        return new PolishVatPayer($client);
    }
}
