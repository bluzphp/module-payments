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
use Bluz\Proxy\Layout;

/**
 * @privilege Management
 *
 * @return mixed
 */
return function () {
    /**
     * @var Controller $this
     */
    Layout::setTemplate('dashboard.phtml');
    Layout::breadCrumbs(
        [
            Layout::ahref('Dashboard', ['dashboard', 'index']),
            __('Payments')
        ]
    );
    $grid = new Payments\Grid();
    $this->assign('grid', $grid);
};
