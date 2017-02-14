<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service\Assets;

use Assetic\Asset\AssetInterface;
use Assetic\AssetWriter;
use Assetic\Util\VarUtils;
use Zoolanders\Framework\Container\Container;

class Writer extends AssetWriter
{
    /**
     * @var Container
     */
    protected $container;

    protected $dir;

    protected $values;

    protected $paths = [];

    /**
     * Writer constructor.
     * @param Container $container
     * @param array $dir
     * @param array $values
     */
    public function __construct(Container $container, $dir, $values = [])
    {
        parent::__construct($dir);

        $this->values = $values;
        $this->dir = $dir;

        $this->container = $container;
    }

    public function writeAsset(AssetInterface $asset)
    {
        foreach (VarUtils::getCombinations($asset->getVars(), $this->values) as $combination) {
            $asset->setValues($combination);

            $path = $this->dir . '/' . VarUtils::resolve(
                    $asset->getTargetPath(),
                    $asset->getVars(),
                    $asset->getValues()
                );

            $this->writeAssetFile(
                $path,
                $asset->dump()
            );

            $this->paths[] = $path;
        }
    }

    public function getPaths()
    {
        return array_unique($this->paths);
    }

    protected function writeAssetFile($path, $contents)
    {
        if (!$this->container->filesystem->has($path)) {
            $this->container->filesystem->write($path, $contents);
        }
    }
}

