<?php

namespace Alchemy\Zippy;

use Alchemy\Zippy\Strategy\FileStrategyInterface;
use Alchemy\Zippy\Strategy\TarFileStrategy;
use Alchemy\Zippy\Strategy\TarGzFileStrategy;
use Alchemy\Zippy\Strategy\ZipFileStrategy;

class Factory
{
    private $strategies = array();

    public function addStrategy(FileStrategyInterface $strategy)
    {
        $this->strategies[$strategy->getFileExtension()] = $strategy;
    }

    public function getAdapterFor($format)
    {
        foreach ($this->strategies[$format]->getAdapters() as $adapter) {
            if ($adapter->isSupported()) {
                return $adapter;
            }
        }
    }

    public static function create()
    {
        $factory = new static();

        $container = AdapterContainer::load();

        $factory->addStrategy(new ZipFileStrategy($container));
        $factory->addStrategy(new TarFileStrategy($container));
        $factory->addStrategy(new TarGzFileStrategy($container));

        return $factory;
    }
}
