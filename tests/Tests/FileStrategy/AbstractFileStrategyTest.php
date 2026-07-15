<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\Adapter\AdapterContainer;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Exception\RuntimeException;

class AbstractFileStrategyTest extends TestCase
{
    public function testGetAdaptersWithNoDefinedServices()
    {
        $this->expectException(\InvalidArgumentException::class);

        $container = AdapterContainer::load();

        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\FileStrategy\AbstractFileStrategy', array($container));
        $stub->expects($this->any())
            ->method('getServiceNames')
            ->will($this->returnValue(array(
                'Unknown\Services'
            )));


        $adapters = $stub->getAdapters();
        $this->assertIsArray($adapters);
        $this->assertCount(0, $adapters);
    }

    public function testGetAdapters()
    {
        $container = AdapterContainer::load();

        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\FileStrategy\AbstractFileStrategy', array($container));
        $stub->expects($this->any())
            ->method('getServiceNames')
            ->will($this->returnValue(array(
                'Alchemy\\Zippy\\Adapter\\ZipAdapter',
                'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'
            )));

        $adapters = $stub->getAdapters();
        $this->assertIsArray($adapters);
        $this->assertCount(2, $adapters);
        foreach ($adapters as $adapter) {
            $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\AdapterInterface', $adapter);
        }
    }

    public function testGetAdaptersWithAdapterThatRaiseAnException()
    {
        $adapterMock = $this->getMockBuilder('\Alchemy\Zippy\Adapter\AdapterInterface')->getMock();
        $container = $this->getMockBuilder('\Alchemy\Zippy\Adapter\AdapterContainer')->getMock();
        $container
            ->method('offsetGet')
            ->will($this->returnCallback(function ($serviceName) use ($adapterMock) {
                if ('Alchemy\\Zippy\\Adapter\\ZipAdapter' === $serviceName) {
                    return $adapterMock;
                }

                throw new RuntimeException();
            }));

        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\FileStrategy\AbstractFileStrategy', array($container));
        $stub->expects($this->any())
            ->method('getServiceNames')
            ->will($this->returnValue(array(
                'Alchemy\\Zippy\\Adapter\\ZipAdapter',
                'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'
            )));

        $adapters = $stub->getAdapters();
        $this->assertIsArray($adapters);
        $this->assertCount(1, $adapters);
        foreach ($adapters as $adapter) {
            $this->assertSame($adapterMock, $adapter);
        }
    }   
}
