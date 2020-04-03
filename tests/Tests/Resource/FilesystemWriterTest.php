<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Resource\Reader\Stream\StreamReader;
use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\Writer\FilesystemWriter;
use Alchemy\Zippy\Tests\TestCase;

class FilesystemWriterTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testWriteFromReader()
    {
        $resource = new Resource(fopen(__FILE__, 'r'), fopen(__FILE__, 'r'));
        $reader = new StreamReader($resource);

        $streamWriter = new FilesystemWriter();

        $streamWriter->writeFromReader($reader, sys_get_temp_dir().'/stream/writer/test.php');
        $streamWriter->writeFromReader($reader, sys_get_temp_dir().'/test.php');
    }
}
