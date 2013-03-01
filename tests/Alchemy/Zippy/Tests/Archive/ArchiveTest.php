<?php

namespace Alchemy\Zippy\Tests\Archive;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Archive\ArchiveInterface;
use Alchemy\Zippy\Archive\Archive;

class ArchiveTest extends TestCase
{
    public function testNewInstance()
    {
        $archive = new Archive($this->getResource('location'), $this->getAdapterMock(), $this->getResourceManagerMock());

        $this->assertTrue($archive instanceof ArchiveInterface);

        return $archive;
    }

    public function testCount()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->will($this->returnValue(array('1', '2')));

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());

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

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());

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

        $collection = $this->getMockBuilder('Alchemy\Zippy\Resource\ResourceCollection')
            ->disableOriginalConstructor()
            ->getMock();

        $collection->expects($this->any())
            ->method('getContext')
            ->will($this->returnValue(getcwd()));

        $collection->expects($this->once())
            ->method('map')
            ->will($this->returnValue(array('hello')));

        $resourceManager = $this->getResourceManagerMock();

        $resourceManager->expects($this->once())
            ->method('handle')
            ->with($this->equalTo(getcwd()), $this->equalTo('hello'))
            ->will($this->returnValue($collection));

        $archive = new Archive($this->getResource('location'), $mockAdapter, $resourceManager);

        $this->assertEquals($archive, $archive->addMembers('hello'));
    }

    public function testRemoveMember()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('remove');

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());

        $this->assertEquals($archive, $archive->removeMembers('hello'));
    }

    private function getAdapterMock()
    {
        return $this->getMock('Alchemy\Zippy\Adapter\AdapterInterface');
    }
}
