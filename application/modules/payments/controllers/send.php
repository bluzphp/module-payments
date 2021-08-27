<?php

/**
 * Generated controller
 *
 * @author   dev
 * @created  2018-01-11 13:16:19
 */

namespace Application;

use Application\Options;
use Bluz\Controller\Controller;
use Bluz\Db\Exception\DbException;
use Bluz\Proxy\Config;
use Bluz\Proxy\Layout;
use Bluz\Proxy\Registry;
use Bluz\Proxy\Router;
use Liqpay\Request;

/**
 * @privilege Pay
 * @return void
 * @throws DbException
 */
return function () {
    /**
     * @var Controller $this
     */
    $privateKey = Config::get('liqpay', 'private_key');
    $publicKey = Config::get('liqpay', 'public_key');
    $sandbox = Config::get('liqpay', 'sandbox');

    $liqpay = new Request($publicKey, $privateKey);
    $liqpay->setCurrency(Request::CURRENCY_UAH);
    $liqpay->setAction(Request::ACTION_PAY);
    // order id is equal to userId + timestamp
    $liqpay->setOrderId($this->user()->getId() . '-' . time());
    // sandbox mode for dev environment
    if ($sandbox) {
        $liqpay->setSandbox();
    }
    $liqpay->setResultUrl(Router::getFullUrl('wallet', 'index'));
    $liqpay->setServerUrl(Router::getFullUrl('payments', 'liqpay'));
    $liqpay->setLanguage(Registry::get('language') ?? 'uk');

    $this->assign('liqpay', $liqpay);
    $this->assign('price', Options\Table::get('price'));
};
