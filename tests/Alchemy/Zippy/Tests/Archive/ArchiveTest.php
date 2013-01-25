<?php

namespace Alchemy\Zippy\Tests\Archive;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Archive\ArchiveInterface;
use Alchemy\Zippy\Archive\Archive;

class ArchiveTest extends TestCase
{
    public function testNewInstance()
    {
        $archive = new Archive('location', $this->getAdapterMock(), $this->getResource('location'));

        $this->assertTrue($archive instanceof ArchiveInterface);

        return $archive;
    }

    /**
     * @depends testNewInstance
     */
    public function testGetPath($archive)
    {
        $this->assertEquals('location', $archive->getPath());
    }

    public function testCount()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->will($this->returnValue(array('1', '2')));

        $archive = new Archive('location', $mockAdapter, $this->getResource('location'));

        $this->assertEquals(2, count($archive));
    }

    public function testGetMembers()
    {
        $mockAdapter = $this->getAdapterMock();

        $resource = $this->getResource('location');

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->with($this->equalTo($resource))
            ->will($this->returnValue(array('1', '2')));

        $archive = new Archive('location', $mockAdapter, $resource);

        $members = $archive->getMembers();

        $this->assertTrue(is_array($members));
        $this->assertEquals(2, count($members));
    }

    public function testAddMembers()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('add');

        $archive = new Archive('location', $mockAdapter, $this->getResource('location'));

        $this->assertEquals($archive, $archive->addMembers('hello'));
    }

    public function testRemoveMember()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('remove');

        $archive = new Archive('location', $mockAdapter, $this->getResource('location'));

        $this->assertEquals($archive, $archive->removeMembers('hello'));
    }

    private function getAdapterMock()
    {
        return $this
            ->getMockBuilder('Alchemy\Zippy\Adapter\AdapterInterface')
            ->getmock();
    }
}
