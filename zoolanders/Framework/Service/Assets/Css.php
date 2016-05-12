<?php

namespace Zoolanders\Service\Assets;

use Assetic\Filter\CssMinFilter;
use Assetic\Filter\LessphpFilter;
use Zoolanders\Container\Container;

class Css extends Assets
{
    protected $filters = ['less', 'cssmin'];

    public function __construct(Container $c)
    {
        parent::__construct($c);
        
        $this->filterManager->set('cssmin', new CssMinFilter());
        $this->filterManager->set('less', new LessphpFilter());
    }

    protected function loadFile($path)
    {
        $this->container->system->document->addStylesheet($path);
    }
}