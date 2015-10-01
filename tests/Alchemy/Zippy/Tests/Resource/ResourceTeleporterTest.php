<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\ResourceTeleporter;

class ResourceTeleporterTest extends TestCase
{
    public function testConstruct()
    {
        $container = $this->getMockBuilder('Alchemy\Zippy\Resource\TeleporterContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $teleporter = new ResourceTeleporter($container);

        return $teleporter;
    }

    public function testTeleport()
    {
        $context = 'supa-context';
        $resource = $this->getMockBuilder('Alchemy\Zippy\Resource\Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->getMockBuilder('Alchemy\Zippy\Resource\TeleporterContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $teleporter = $this->getMock('Alchemy\Zippy\Resource\Teleporter\TeleporterInterface');
        $teleporter->expects($this->once())
            ->method('teleport')
            ->with($this->equalTo($resource), $this->equalTo($context));

        $container->expects($this->once())
            ->method('fromResource')
            ->with($this->equalTo($resource))
            ->will($this->returnValue($teleporter));

        $resourceTeleporter = new ResourceTeleporter($container);
        $resourceTeleporter->teleport($context, $resource);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Alchemy\Zippy\Resource\ResourceTeleporter', ResourceTeleporter::create());
    }
}
