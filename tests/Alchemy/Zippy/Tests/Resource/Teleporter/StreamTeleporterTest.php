<?php

namespace Alchemy\Zippy\Tests\Resource\Teleporter;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\Teleporter\StreamTeleporter;
use Symfony\Component\Filesystem\Exception\IOException;

class StreamTeleporterTest extends TeleporterTestCase
{
    /**
     * @dataProvider provideContexts
     */
    public function testTeleport($context)
    {
        $teleporter = StreamTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource(fopen(__FILE__, 'rb'), $target);

        if (is_file($target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertfileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    /**
     * @dataProvider provideNotExistingContexts
     * @expectedException \Alchemy\Zippy\Exception\IOException
     */
    public function testTeleportFail($context)
    {
        $fs = $this->getMockBuilder('\Symfony\Component\Filesystem\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();

        $fs->expects($this->any())->method('dumpFile')->will($this->throwException(
            new IOException('')
        ));
        $teleporter = new StreamTeleporter($fs);

        $target = 'plop-badge.php';
        $resource = new Resource(fopen(__FILE__, 'rb'), $target);

        if (is_file($target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);
    }

    /**
     * @dataProvider provideContexts
     */
    public function testTeleportInNonStreamMode($context)
    {
        $teleporter = StreamTeleporter::create();

        $target = 'plop-badge.php';
        $resource = new Resource(__FILE__, $target);

        if (is_file($target)) {
            unlink($context . '/' . $target);
        }

        $teleporter->teleport($resource, $context);

        $this->assertfileExists($context . '/' . $target);
        unlink($context . '/' . $target);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Alchemy\Zippy\Resource\Teleporter\StreamTeleporter', StreamTeleporter::create());
    }
}
