<?php

/**
 * @namespace
 */

namespace Application\Payments;

use Application\Options\Table as OptionsTable;
use Application\Payments\Table as PaymentsTable;
use Application\Transactions\Table as TransactionsTable;

class Service
{
    /**
     * Add Debit record
     *
     * @param int $userId
     * @param array $data
     *
     * @return \Application\Wallets\Row|bool
     */
    public static function addLiqpayPayment(int $userId, array $data)
    {
        return Db::transaction(function () use ($userId, $data) {

            $value = ceil($data['amount'] / OptionsTable::get('price')) * 1000;

            $transaction = TransactionsTable::create();
            $transaction->userId = $userId;
            $transaction->amount = $value;
            $transaction->type = TransactionsTable::TYPE_DEBIT;
            $transaction->save();

            $wallet = self::getWallet($userId);
            $wallet->amount += $transaction->amount;
            $wallet->save();

            $payment = PaymentsTable::create();
            $payment->amount = $data['amount'];
            $payment->currency = $data['currency'];
            $payment->provider = PaymentsTable::PROVIDER_LIQPAY;
            $payment->foreignId = $data['payment_id'];
            $payment->transactionId = $transaction->id;
            $payment->rawData = \json_encode($data);
            $payment->save();

            return $transaction;
        });
    }
}