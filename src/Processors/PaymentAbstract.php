<?php

namespace SoftInvest\PaymentProcessing\Processors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentAbstract implements IPayment
{
    protected static string $logChannel = 'emergency';

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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

    public function process(): string|bool
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

    public function outputSuccess(): void
    {
    }

    public function outputFail(): void
    {
    }
}
