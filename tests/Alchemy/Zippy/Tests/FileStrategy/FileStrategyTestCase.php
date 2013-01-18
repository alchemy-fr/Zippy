<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\Adapter\AdapterContainer;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\FileStrategy\FileStrategyInterface;

abstract class FileStrategyTestCase extends TestCase
{
    /** @test */
    public function getFileExtensionShouldReturnAnString()
    {
        $extension = $this->getStrategy()->getFileExtension();

        $this->assertNotEquals('', trim($extension));
        $this->assertInternalType('string', $extension);
    }

    /** @test */
    public function getAdaptersShouldReturnAnArrayOfAdapter()
    {
        $adapters = $this->getStrategy()->getAdapters();

        $this->assertInternalType('array', $adapters);

        foreach ($adapters as $adapter) {
            $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\AdapterInterface', $adapter);
        }
    }

    protected function getContainer()
    {
        return AdapterContainer::load();
    }

    /**
     * @return FileStrategyInterface
     */
    abstract protected function getStrategy();
}
