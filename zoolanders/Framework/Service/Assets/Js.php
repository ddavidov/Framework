<?php

namespace Zoolanders\Service\Assets;

use Assetic\Filter\JSMinFilter;
use Zoolanders\Container\Container;

class Js extends Assets
{
    protected $filters = ['jsmin'];
    
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->filterManager->set('jsmin', new JSMinFilter());
    }

    protected function loadFile($path)
    {
        $this->container->system->document->addScript($path);
    }
}