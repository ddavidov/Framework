<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Container\Container;

/**
 * Class Request
 * @package Zoolanders\Service
 */
class Request extends Service
{
    /**
     * @var \JInput
     */
    protected $input;

    /**
     * Request constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->input = new \JInput;
    }

    /**
     * Proxy function calls to the JInput object
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->input, $name], $arguments);
    }

    /**
     * isAjax
     *
     * @return bool True if an ajax call is being made
     */
    public function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
