<?php

namespace Alchemy\Zippy\Tests\Archive;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Archive\ArchiveInterface;
use Alchemy\Zippy\Archive\Archive;

class ArchiveTest extends TestCase
{
    public function testNewInstance()
    {
        $archive = new Archive('location', $this->getAdapterMock());

        $this->assertTrue($archive instanceof ArchiveInterface);

        return $archive;
    }

    /**
     * @depends testNewInstance
     */
    public function testGetLocation($archive)
    {
        $this->assertEquals('location', $archive->getLocation());
    }

    public function testCount()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->will($this->returnValue(array('1', '2')));

        $archive = new Archive('location', $mockAdapter);

        $this->assertEquals(2, count($archive));
    }

    public function testGetMembers()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->with($this->equalTo('location'))
            ->will($this->returnValue(array('1', '2')));

        $archive = new Archive('location', $mockAdapter);

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

        $archive = new Archive('location', $mockAdapter);

        $this->assertEquals($archive, $archive->addMembers('hello'));
    }

    public function testRemoveMember()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('remove');

        $archive = new Archive('location', $mockAdapter);

        $this->assertEquals($archive, $archive->removeMembers('hello'));
    }

    private function getAdapterMock()
    {
        return $this
            ->getMockBuilder('Alchemy\Zippy\Adapter\AdapterInterface')
            ->getmock();
    }
}
