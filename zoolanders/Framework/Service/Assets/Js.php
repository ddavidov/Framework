<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service\Assets;

use Assetic\Filter\JSMinFilter;
use Zoolanders\Framework\Container\Container;

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