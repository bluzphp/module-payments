<?php

/**
 * LiqPay Callback
 *
 * @author   Anton Shevchuk
 * @created  2018-01-11 13:16:04
 * @link     https://www.liqpay.ua/documentation/api/callback
 */

namespace Application;

use Bluz\Controller\Controller;
use Bluz\Proxy\Config;
use Bluz\Proxy\Logger;
use Liqpay\Request;

/**
 * @param $liqpay_token
 *
 * @return void
 */
return function ($liqpay_token) {
    /**
     * @var Controller $this
     */

    $privateKey = Config::get('liqpay', 'private_key');
    $publicKey = Config::get('liqpay', 'public_key');

//    $request = new Request($publicKey, $privateKey);
//    $request->setAction(Request::ACTION_3DS_VERIFY);

    $liqpay = new \LiqPay($publicKey, $privateKey);
    $res = $liqpay->api('request', [
        'action'  => 'confirm',
        'version' => '3',
        'confirm_token'   => $liqpay_token
    ]);

    Logger::info('3DS: ' . json_encode($res));
};
