<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Adapter\AdapterContainer;

class AdapterContainerTests extends TestCase
{
    /** @test */
    public function itShouldRegisterAdaptersOnload()
    {
        $that = $this;
        $container = $this->getMock('Alchemy\Zippy\Adapter\AdapterContainer');
        $container
            ->expects($this->any())
            ->method('offsetGet')
            ->will($this->returnCallback(function($offset) use ($that) {
                if (array_key_exists('Alchemy\Zippy\Adapter\AdapterInterface', class_implements($offset))) {
                    return $that->getMockBuilder($offset)
                            ->disableOriginalConstructor()
                            ->getMock();
                }

                return null;
            }));

        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\ZipAdapter', $container['Alchemy\\Zippy\\Adapter\\ZipAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\GNUTar\\TarGNUTarAdapter', $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarGNUTarAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\GNUTar\\TarGzGNUTarAdapter', $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarGzGNUTarAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\GNUTar\\TarBz2GNUTarAdapter', $container['Alchemy\\Zippy\\Adapter\\GNUTar\\TarBz2GNUTarAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\BSDTar\\TarGzBSDTarAdapter', $container['Alchemy\\Zippy\\Adapter\\BSDTar\\TarGzBSDTarAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\BSDTar\\TarBSDTarAdapter', $container['Alchemy\\Zippy\\Adapter\\BSDTar\\TarBSDTarAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\BSDTar\\TarBz2BSDTarAdapter', $container['Alchemy\\Zippy\\Adapter\\BSDTar\\TarBz2BSDTarAdapter']);
    }
}
