<?php

namespace SoftInvest\PaymentProcessing;

use Illuminate\Http\Request;
use SoftInvest\PaymentProcessing\Processors\IPayment;
use SoftInvest\PaymentProcessing\Processors\PaymentAbstract;

class Processor
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string ...$classNames
     * @return IPayment|null
     */
    public function detect(string ...$classNames): ?IPayment
    {
        $request = $this->getRequest();
        foreach ($classNames as $className) {
            $payment = new $className($request);
            if ($payment->detect()) {
                return $payment;
            }
        }

        return null;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @param string $driver
     *
     * @return ?PaymentAbstract
     */
    public static function findPaymentSystemByDriverName(string $driver): ?PaymentAbstract
    {
        $arr = [];
        if ($handle = opendir(base_path('app') . '/Components/PaymentProcessors')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $entry = str_replace('.php', '', $entry);

                    $className = '\\App\\Components\\PaymentProcessors\\' . ucfirst($entry);
                    $obj = new $className(new Request());
                    if (method_exists($obj, 'isSupported') && $obj->isSupported($driver)) {
                        return $obj;
                    }
                }
            }
            closedir($handle);
        }
        return null;
    }
}
