<?php

use Phinx\Migration\AbstractMigration;

class Payments extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function change()
    {
        $table = $this->table('payments');
        $table
            ->addColumn('transactionId', 'integer')
            ->addForeignKey('transactionId', 'transactions', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE'
            ])
            ->addColumn('amount', 'float')
            ->addColumn('currency', 'enum', ['values' => ['UAH', 'USD', 'EUR']])
            ->addColumn('exchange', 'float')
            ->addColumn('provider', 'enum', ['values' => ['liqpay', 'paypal', 'stripe']])
            ->addColumn('foreignId', 'string', ['length' => 255])
            ->addColumn('rawData', 'text')
            ->addTimestamps('created', 'updated')
            ->create();
    }
}
