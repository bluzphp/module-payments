<?php

/**
 * Grid controller for Payments model
 *
 * @author   dev
 * @created  2017-11-02 12:27:35
 */

/**
 * @namespace
 */
namespace Application;

use Bluz\Controller\Controller;
use Bluz\Grid\Grid;
use Bluz\Grid\GridException;

/**
 * @privilege Info
 *
 * @return void
 * @throws GridException
 */
return function () {
    /**
     * @var Controller $this
     */
    $grid = new Payments\Grid();
    $grid->addFilter('users.id', Grid::FILTER_EQ, $this->user()->getId());

    $this->assign('grid', $grid);
    $this->assign('user', $this->user());
};
