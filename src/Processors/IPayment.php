<?php

namespace SoftInvest\PaymentProcessing\Processors;

interface IPayment
{
    public function calcSign(): string;

    public function detect(): bool;

    public function process(): string|bool;

    public function isAllowedIp(): bool;

    public function outputFail(): void;

    public function outputSuccess(): void;
}
