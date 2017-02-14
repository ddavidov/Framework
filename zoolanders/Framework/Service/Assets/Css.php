<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service\Assets;

use Assetic\Filter\CssMinFilter;
use Assetic\Filter\LessphpFilter;
use Zoolanders\Framework\Container\Container;

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