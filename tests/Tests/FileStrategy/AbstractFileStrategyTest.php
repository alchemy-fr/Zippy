<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\Adapter\AdapterContainer;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Exception\RuntimeException;

class AbstractFileStrategyTest extends TestCase
{
    /**
     * @expectedException   \InvalidArgumentException
     */
    public function testGetAdaptersWithNoDefinedServices()
    {
        $container = AdapterContainer::load();

        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\FileStrategy\AbstractFileStrategy', array($container));
        $stub->expects($this->any())
            ->method('getServiceNames')
            ->will($this->returnValue(array(
                'Unknown\Services'
            )));


        $adapters = $stub->getAdapters();
        $this->assertInternalType('array', $adapters);
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
        $this->assertInternalType('array', $adapters);
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
            ->expects($this->at(0))
            ->method('offsetGet')
            ->with($this->equalTo('Alchemy\\Zippy\\Adapter\\ZipAdapter'))
            ->will($this->returnValue($adapterMock));

        $container
            ->expects($this->at(1))
            ->method('offsetGet')
            ->with($this->equalTo('Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'))
            ->will($this->throwException(new RuntimeException()));

        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\FileStrategy\AbstractFileStrategy', array($container));
        $stub->expects($this->any())
            ->method('getServiceNames')
            ->will($this->returnValue(array(
                'Alchemy\\Zippy\\Adapter\\ZipAdapter',
                'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'
            )));

        $adapters = $stub->getAdapters();
        $this->assertInternalType('array', $adapters);
        $this->assertCount(1, $adapters);
        foreach ($adapters as $adapter) {
            $this->assertSame($adapterMock, $adapter);
        }
    }   
}
