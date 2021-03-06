<?php

/**
 * Generated controller
 *
 * @author   dev
 * @created  2018-01-12 18:30:55
 */

namespace Application;

use Bluz\Controller\Controller;
use Bluz\Db\Exception\RelationNotFoundException;
use Bluz\Db\Exception\TableNotFoundException;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Http\Exception\NotFoundException;
use Bluz\Proxy\Layout;

/**
 * @privilege Info
 *
 * @param int $id
 *
 * @return array
 * @throws ForbiddenException
 * @throws NotFoundException
 * @throws RelationNotFoundException
 * @throws TableNotFoundException
 */
return function ($id = null) {
    /**
     * @var Controller $this
     */
    $payment = Payments\Table::findRow($id);

    if (!$payment) {
        throw new NotFoundException('Payment not found');
    }

    if (
        $payment->getUser()->getId() != $this->user()->getId()
        && !$this->user()->hasPrivilege('payments', 'Management')
    ) {
        throw new ForbiddenException();
    }

    return ['payment' => $payment];
};
