<?php

namespace Alchemy\Zippy\Strategy;

use Alchemy\Zippy\AdapterContainer;

class TarGzFileStrategy implements FileStrategyInterface
{
    private $container;

    public function __construct(AdapterContainer $container)
    {
        $this->container = $container;
    }

    public function getAdapters()
    {
        return array(
            $this->container['Alchemy\\Zippy\\Adapter\\GNUTarAdapter'],
        );
    }

    public function getFileExtension()
    {
        return 'tar.gz';
    }
}
