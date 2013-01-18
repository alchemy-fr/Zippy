<?php

namespace Alchemy\Zippy\Tests;

use Alchemy\Zippy\AdapterContainer;

class AdapterContainerTests extends TestCase
{
    /** @test */
    public function itShouldRegisterAdaptersOnload()
    {
        $container = AdapterContainer::load();

        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\ZipAdapter', $container['Alchemy\\Zippy\\Adapter\\ZipAdapter']);
        $this->assertInstanceOf('Alchemy\\Zippy\\Adapter\\GNUTarAdapter', $container['Alchemy\\Zippy\\Adapter\\GNUTarAdapter']);
    }
}
