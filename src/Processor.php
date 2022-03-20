<?php

namespace SoftInvest\PaymentProcessing;

use App\Components\PaymentProcessing\Processors\IPayment;
use Illuminate\Http\Request;

class Processor
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function detect(string ...$classNames): IPayment|false
    {
        $request = $this->getRequest();
        foreach ($classNames as $className) {
            $payment = new $className($request);
            if ($payment->detect()) {
                return $payment;
            }
        }

        return false;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
