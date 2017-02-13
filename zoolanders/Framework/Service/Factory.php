<?php

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Response\ResponseInterface;
use Zoolanders\Framework\View\ViewInterface;

/**
 * Class Factory
 * @package Zoolanders\Framework\Service
 */
class Factory extends Service
{
    /**
     * Make response
     *
     * @param   Input
     *
     * @return  ResponseInterface
     */
    public function response($input)
    {
        $type = $input->isAjax() ? 'Json' : 'Html';

        $responseClass = '\Zoolanders\Framework\Response\\' . $type . 'Response';

        return $this->container->make($responseClass);
    }

    /**
     * Make response
     *
     * @param   Input
     *
     * @return  ViewInterface
     */
    public function view($input, $config = [])
    {
        $type = $input->isAjax() ? 'Json' : 'Html';
        $name = $config['view_name'];

        $viewClass = $this->container->environment->getRootNamespace() . 'View\'' . ucfirst($name) . '\\' . $type;

        if(!class_exists($viewClass)){
            // Fallback to core view:
            $viewClass = '\Zoolanders\Framework\View\\' . $type . 'View';
        }

        return $this->container->make($viewClass, $config);
    }
}
