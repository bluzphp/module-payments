<?php

/**
 * @namespace
 */

namespace Application\Payments;

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

        $this->setAdapter($adapter);
        $this->setDefaultLimit(25);
        $this->setAllowOrders([]);
    }
}
