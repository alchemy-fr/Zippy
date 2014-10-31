<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Tests\TestCase;

class ResourceTest extends TestCase
{
    public function testGetTargetAndOriginal()
    {
        $original = 'original-style';
        $target = 'target-fishnet';

        $resource = new Resource($original, $target);

        $this->assertEquals($original, $resource->getOriginal());
        $this->assertEquals($target, $resource->getTarget());
    }

    /**
     * @dataProvider provideProcessInPlaceData
     */
    public function testCanBeProcessedInPlace($expected, $context, $original, $target)
    {
        $resource = new Resource($original, $target);

        $this->assertInternalType('boolean', $resource->canBeProcessedInPlace($context));
        $this->assertEquals($expected, $resource->canBeProcessedInPlace($context));
    }

    public function provideProcessInPlaceData()
    {
        return array(
            array(true, '/path/to', '/path/to/file1', 'file1'),
            array(true, __DIR__, __FILE__, basename(__FILE__)),
            array(false, __DIR__, fopen(__FILE__, 'rb'), basename(__FILE__)),
            array(false, '/path/to', 'ftp:///path/to/file1', 'file1'),
            array(false, '/path/to', '/path/file1', 'file1'),
            array(false, '/path/to', 'file:///path/file1', 'file1'),
            array(true, '/path', '/path/to/file1', 'to/file1'),
            array(true, '/path/to', '/path/to/subdir/file2', 'subdir/file2'),
            array(true, '/path/to', 'file:///path/to/subdir/file2', 'subdir/file2'),
        );
    }

    /**
     * @dataProvider provideLocalResourcesOrNot
     */
    public function testGetContextForProcessInSinglePlace($original, $expected)
    {
        $resource = new Resource($original, basename(__FILE__));
        $this->assertEquals($expected, $resource->getContextForProcessInSinglePlace());
    }

    public function provideLocalResourcesOrNot()
    {
        return array(
            array('ftp:///path/to/file', null),
            array('http:///path/to/file', null),
            array(__FILE__, __DIR__),
            array('file:///path/to/file', null),
        );
    }
}
