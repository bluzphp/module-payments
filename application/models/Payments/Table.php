<?php

/**
 * @namespace
 */

namespace Application\Payments;

/**
 * Class Table for `payments`
 *
 * @package  Application\Payments
 *
 * @method   static Row create(array $data = [])
 * @see      \Bluz\Db\Table::create()
 * @method   static Row findRow($primaryKey)
 * @see      \Bluz\Db\Table::findRow()
 * @method   static Row findRowWhere($whereList)
 * @see      \Bluz\Db\Table::findRowWhere()
 *
 * @author   dev
 * @created  2017-11-02 10:31:59
 */
class Table extends \Bluz\Db\Table
{
    public const PROVIDER_LIQPAY = 'liqpay';
    public const PROVIDER_PAYPAL = 'paypal';
    public const PROVIDER_STRIPE = 'stripe';

    /**
     * @var string
     */
    protected $name = 'payments';

    protected $rowClass = Row::class;

    /**
     * Primary key(s)
     * @var array
     */
    protected $primary = ['id'];

    /**
     * init
     *
     * @return void
     */
    public function init(): void
    {
        $this->linkTo('transactionId', 'Transactions', 'id');
    }
}
