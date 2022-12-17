<?php

namespace SoftInvest\PaymentProcessing\Processors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentAbstract implements IPayment
{
    protected array $supports = [
    ];

    protected static string $logChannel = 'emergency';

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function initiatePayment(int $userId, int $paymentSystemId, string $driver,
                                    string $currency, int $qty,
                                    float $amount,float $totalAmount, int $productTypeId,
                                    string $projectUuid, array $payLoad):?string{
        return null;

    }

    protected function log(string $message): void
    {
        Log::channel(static::$logChannel)->info($message);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function isAllowedIp(): bool
    {
        return false;
    }

    public function calcSign(): string
    {
        return '';
    }

    public function detect(): bool
    {
        return false;
    }

    public function process(): ?string
    {
        return false;
    }

    public function outputResult(bool $result): void
    {
        if ($result) {
            $this->outputSuccess();
        } else {
            $this->outputFail();
        }
    }

    public function isSupported(string $driver):bool{
        if (!$this->supports){
            return false;
        }
        return in_array($driver, $this->supports);
    }

    public function outputSuccess(): void
    {
    }

    public function outputFail(): void
    {
    }
}
