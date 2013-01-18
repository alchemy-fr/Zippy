<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Adapter\AdapterContainer;

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
