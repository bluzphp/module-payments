<?php

/**
 * @namespace
 */

namespace Application\Payments;

use Application\Wallets\Table;
use Bluz\Grid\Source\SelectSource;

/**
 * Grid based on Table
 *
 * @package  Application\Payments
 *
 * @author   dev
 * @created  2017-11-02 11:38:54
 */
class Grid extends \Bluz\Grid\Grid
{
    /**
     * @var string
     */
    protected $uid = 'payments';

    /**
     * @return void
     * @throws \Bluz\Grid\GridException
     */
    public function init(): void
    {
        // Current table as source of grid
        $adapter = new SelectSource();
        $adapter->setSource(Table::select());
        $adapter->setSource(
            Table::select()
                ->addSelect('users.login as login')
                ->join('payments', 'transactions', 'transactions', 'transactions.id = payments.transactionId')
                ->join('transactions', 'users', 'users', 'users.id = transactions.userId')
        );

        $this->addAlias('users.id', 'user');
        $this->addAlias('users.login', 'login');
        $this->setAdapter($adapter);
        $this->setDefaultLimit(25);
        $this->setAllowFilters(['amount', 'currency', 'provider', 'users.id', 'users.login', 'created']);
        $this->setAllowOrders(['id', 'amount', 'currency', 'provider', 'users.id', 'users.login', 'created']);
    }
}
