<?php


namespace SoftInvest\PaymentProcessing\Processors\Alikassa;

use SoftInvest\PaymentProcessing\Processors\Alikassa\Exceptions\EInvalidCert;

class Payout
{
    const PM_CARD_RUB = 'payment_card_rub';

    /**
     * @throws EInvalidCert
     */
    public function calcSign(string $privateCert, string $password, string $data): string
    {
        $privateKey = \openssl_pkey_get_private(
            file_get_contents($privateCert),
            file_get_contents($password)
        );

        if ($privateKey === false) {
            throw new EInvalidCert();
        }

        \openssl_sign($data, $sign, $privateKey);

        return base64_encode($sign);
    }

    /**
     * @param string $method
     * @param string $account
     * @param string $sign
     * @param string $data
     * @return mixed|null
     */
    public function request(string $method, string $account, string $sign, string $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api-merchant.alikassa.com/' . $method);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Account: ' . $account,
            'Sign: ' . $sign,
        ]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AliKassa2.0 API');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        return $response ? json_decode($response, true) : null;
    }

    /**
     * @param $orderId
     * @param float $amount
     * @param string $payerAcc
     * @param string $service
     * @param string $account
     * @return mixed|null
     * @throws EInvalidCert
     */
    public function createPayout($orderId, float $amount, string $payerAcc, string $service, string $account)
    {
        $data = json_encode([
            'order_id' => $orderId,
            'amount' => round($amount, 2),
            'number' => $payerAcc,
            // 'notification_endpoint_id' => 5,
            'service' => $service,
        ]);
        $sign = $this->calcSign(
            __DIR__ . '/Certs/payouts/private.pem',
            __DIR__ . '/Certs/payouts/password.txt',
            $data
        );
        /*{
           "payment_status": "wait",
           "id": 100001524
        }*/
        return $this->request('v1/payout', $account, $sign, $data);
    }

    /**
     * @param $orderId
     * @param string $account
     * @return mixed|null
     * @throws EInvalidCert
     */
    public function requestStatus($orderId, string $account)
    {
        $data = json_encode([
            'order_id' => $orderId
        ]);
        $sign = $this->calcSign(
            __DIR__ . '/Certs/payouts/private.pem',
            __DIR__ . '/Certs/payouts/password.txt',
            $data
        );
        /*{
           "payment_status": "paid",
           "id": 100000536,
           "order_id": "6422494",
           "amount": 300,
           "account_payment_amount": 290,
           "commission_amount": 10,
           "service_code": "payment_card_rub",
           "account_old_balance": 1290,
           "account_new_balance": 1000
        }*/
        return $this->request('v1/payout/status', $account, $sign, $data);
    }

    /**
     * @param $orderId
     * @param string $account
     * @return mixed|null
     * @throws EInvalidCert
     */
    public function requestBalance($orderId, string $account)
    {
        $data = json_encode([
            'order_id' => $orderId
        ]);
        $sign = $this->calcSign(
            __DIR__ . '/Certs/payouts/private.pem',
            __DIR__ . '/Certs/payouts/password.txt',
            $data
        );
        /*{
           "currency": "RUB",
           "balance": 93.84000000,
           "hold": 0.00000000,
           "rolling_reserve": 0.00000000
        }*/
        return $this->request('v1/account/balance', $account, $sign, $data);
    }

}