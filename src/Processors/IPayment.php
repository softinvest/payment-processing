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

    public function initiatePayment(int $userId, int $paymentSystemId, string $driver, string $currency, int $qty,
                                    float $amount,float $totalAmount, int $productTypeId, string $projectUuid,
                                    array $payLoad):?string;
}
