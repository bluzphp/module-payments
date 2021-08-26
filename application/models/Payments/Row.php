<?php

/**
 * @namespace
 */

namespace Application\Payments;

use Application\Transactions;
use Application\Users;

/**
 * Class Row for `payments`
 *
 * @package  Application\Payments
 *
 * @property integer $id
 * @property integer $transactionId
 * @property string $amount
 * @property string $currency
 * @property string $exchange
 * @property string $provider
 * @property string $foreignId
 * @property string $rawData
 * @property string $created
 * @property string $updated
 *
 * @author   dev
 * @created  2017-11-02 10:31:59
 */
class Row extends \Bluz\Db\Row
{
    /**
     * @return void
     */
    public function beforeInsert(): void
    {
    }

    /**
     * @return void
     */
    public function beforeUpdate(): void
    {
    }

    /**
     * getTransaction
     *
     * @return Transactions\Row|null
     * @throws \Bluz\Db\Exception\RelationNotFoundException
     * @throws \Bluz\Db\Exception\TableNotFoundException
     */
    public function getTransaction(): ?Transactions\Row
    {
        return $this->getRelation('Transactions');
    }

    /**
     * getTransaction
     *
     * @return Transactions\Row|null
     * @throws \Bluz\Db\Exception\RelationNotFoundException
     * @throws \Bluz\Db\Exception\TableNotFoundException
     */
    public function getUser(): ?Users\Row
    {
        return $this->getRelation('Transactions')->getUser();
    }
}
