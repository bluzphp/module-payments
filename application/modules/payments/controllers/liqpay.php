<?php

/**
 * LiqPay Callback
 *
 * @author   Anton Shevchuk
 * @created  2018-01-11 13:16:04
 * @link     https://www.liqpay.ua/documentation/api/callback
 */

namespace Application;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Controller\Controller;
use Bluz\Proxy\Config;
use Bluz\Proxy\Logger;
use Bluz\Proxy\Router;

/**
 * @param $data
 * @param $signature
 *
 * @return void
 */
return function ($data, $signature) {
    /**
     * @var Controller $this
     */
    $pKey = Config::get('liqpay', 'private_key');
    $sign = base64_encode(
        sha1(
            $pKey . $data . $pKey,
            1
        )
    );

    if ($signature !== $sign) {
        throw new ApplicationException('Invalid signature');
    }

    Logger::info('liqpay: ' . base64_decode($data));

    $data = json_decode(base64_decode($data), true);

    $pay = false;
    $success = false;

    // process $data['action']
    switch ($data['action']) {
        case 'pay':
            $pay = true;
            break;
        case 'hold':
        case 'paysplit':
        case 'subscribe':
        case 'paydonate':
        default:
            break;
    }

    /**
     * process $data['status']
     * @link https://www.liqpay.ua/ru/doc/status
     */
    Logger::info($data['status']);
    switch ($data['status']) {
        case 'success':
            $success = true;
            break;
        case '3ds_verify':
            Logger::info($data['redirect_to']);
            $this->redirect($data['redirect_to'] . '?return_to=' . Router::getFullUrl('payments', '3ds'));
            break;
        case 'sandbox':
            $success = Config::get('liqpay', 'sandbox');
            break;
        case 'reversed':
            // @todo: add refund
            $data['refund_amount'];
            break;
        case 'wait_accept':
        case 'failure':
        case 'error':
            Logger::error('Payment ' . $data['status']);
            break;
        default:
            // do nothing
            break;
    }

    // processing data for success pay
    if ($pay && $success) {
        list($userId) = explode('-', $data['order_id']);

        $payment = Payments\Table::findRowWhere(
            ['provider' => Payments\Table::PROVIDER_LIQPAY, 'foreignId' => $data['payment_id']]
        );
        if ($payment) {
            // payment already processed
            return;
        }

        Wallets\Table::addLiqpayPayment((int) $userId, $data);
    }
};
