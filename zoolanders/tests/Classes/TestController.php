<?php

namespace ZFTests\Classes;

use Zoolanders\Framework\Controller\Controller;

/**
 * Class TestController
 * @package ZFTests\Classes
 */
class TestController extends Controller
{
    /**
     * Simple test action
     *
     * @return string
     */
    public function ping(){

        return 'test';
    }

    /**
     * Default dummy action
     *
     * @return string
     */
    public function _default(){

        return 'default';
    }
}
