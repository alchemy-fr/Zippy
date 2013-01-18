<?php

namespace Alchemy\Zippy\Strategy;

use Alchemy\Zippy\AdapterContainer;

class ZipFileStrategy implements FileStrategyInterface
{
    private $container;

    public function __construct(AdapterContainer $container)
    {
        $this->container = $container;
    }

    public function getAdapters()
    {
        return array(
            $this->container['Alchemy\\Zippy\\Adapter\\ZipAdapter'],
        );
    }

    public function getFileExtension()
    {
        return 'zip';
    }
}
