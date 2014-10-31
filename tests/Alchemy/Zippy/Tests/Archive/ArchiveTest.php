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
        $resource = $this->getResource('location');

        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('add')
            ->with($this->equalTo($resource), $this->equalTo(array('hello')), $this->equalTo(true));

        $resourceManager = $this->getResourceManagerMock();

        $archive = new Archive($resource, $mockAdapter, $resourceManager);

        $this->assertEquals($archive, $archive->addMembers(array('hello')));
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

    public function testExtract()
    {

        $resource = $this->getResource('location');
        $to = '/directory';

        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('extract')
            ->with($this->equalTo($resource), $this->equalTo($to));

        $archive = new Archive($resource, $mockAdapter, $this->getResourceManagerMock());

        $archive->extract($to);
    }

    public function testExtractMembers()
    {

        $resource = $this->getResource('location');
        $to = '/directory';
        $members = array('/member1', '/member2');

        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('extractMembers')
            ->with($this->equalTo($resource), $this->equalTo($members), $this->equalTo($to));

        $archive = new Archive($resource, $mockAdapter, $this->getResourceManagerMock());

        $archive->extractMembers($members, $to);
    }

    public function testGetIterator()
    {
        $mockAdapter = $this->getAdapterMock();

        $mockAdapter
            ->expects($this->once())
            ->method('listMembers')
            ->will($this->returnValue(array()));

        $archive = new Archive($this->getResource('location'), $mockAdapter, $this->getResourceManagerMock());
        $iterator = $archive->getIterator();
        $this->assertInstanceOf('\Traversable', $iterator);
    }


    private function getAdapterMock()
    {
        return $this->getMock('Alchemy\Zippy\Adapter\AdapterInterface');
    }
}
