<?php

namespace Zoolanders\Service\Assets;

use Assetic\AssetWriter;
use Zoolanders\Container\Container;

class Writer extends AssetWriter
{
    /**
     * @var Container
     */
    protected static $container;

    /**
     * Writer constructor.
     * @param Container $container
     * @param array $dir
     * @param array $values
     */
    public function __construct(Container $container, $dir, array $values = [])
    {
        parent::__construct($dir, $values);

        self::$container = $container;
    }

    public function writeAsset(AssetInterface $asset)
    {

    }

    protected static function write($path, $contents)
    {
        if (!self::$container->filesystem->has($path)) {
            self::$container->filesystem->write($path, $contents);
        }
    }
}

