<?php

namespace SoftInvest\PaymentProcessing\Processors\Alikassa\Exceptions;

class EInvalidCert extends \Exception
{
    protected $message = 'Invalid certificate';
}