<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service\Assets;

use Assetic\AssetManager;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\FilterManager;
use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Service\Service;

abstract class Assets extends Service
{
    /**
     * @var AssetManager
     */
    protected $assetManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var AssetFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var
     */
    protected $assets = [];

    /**
     * Assets constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $this->assetManager = new AssetManager();
        $this->filterManager = new FilterManager();
        $this->factory = new AssetFactory(JPATH_SITE);

        $this->factory->setAssetManager($this->assetManager);
        $this->factory->setFilterManager($this->filterManager);
        $this->factory->addWorker(new CacheBustingWorker());
    }

    public function define($name, $assets)
    {
        settype($assets, 'array');
        $this->assetManager->set($name, $this->factory->createAsset($assets));
    }

    public function add($assets)
    {
        settype($assets, 'array');

        foreach ($assets as &$asset) {
            $asset = $this->container->path->path($asset);
        }

        $this->assets = array_unique(array_merge($this->assets, $assets));
    }

    public function load($filters = false)
    {
        if (!$filters) {
            $filters = $this->filters;
        }

        $asset = $this->factory->createAsset($this->assets, $filters);
        
        $writer = new Writer($this->container, $this->container->path->path('cache:'));
        $writer->writeAsset($asset);

        foreach ($writer->getPaths() as $path) {
            $this->loadFile($path);
        }
    }

    abstract protected function loadFile($path);
}
