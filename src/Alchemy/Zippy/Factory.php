<?php

namespace Alchemy\Zippy;

use Alchemy\Zippy\Strategy\FileStrategyInterface;
use Alchemy\Zippy\Strategy\TarFileStrategy;
use Alchemy\Zippy\Strategy\TarGzFileStrategy;
use Alchemy\Zippy\Strategy\ZipFileStrategy;

class Factory
{
    public $adapters;
    private $strategies = array();

    public function __construct(AdapterContainer $adapters)
    {
        $this->adapters = $adapters;
    }

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
        $adapters = AdapterContainer::load();
        $factory = new static($adapters);

        $factory->addStrategy(new ZipFileStrategy($adapters));
        $factory->addStrategy(new TarFileStrategy($adapters));
        $factory->addStrategy(new TarGzFileStrategy($adapters));

        return $factory;
    }
}
