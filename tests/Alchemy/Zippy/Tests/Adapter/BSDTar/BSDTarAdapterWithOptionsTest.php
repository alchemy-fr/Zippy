<?php

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

use Alchemy\Zippy\Tests\TestCase;

abstract class BSDTarAdapterWithOptionsTest extends TestCase
{
    protected static $tarFile;

    /**
     * @var AbstractBSDTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass()
    {
        $classname = static::getAdapterClassName();
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), $classname::getName());

        if (file_exists(self::$tarFile)) {
            unlink(self::$tarFile);
        }
    }

    public static function tearDownAfterClass()
    {
        if (file_exists(self::$tarFile)) {
            unlink(self::$tarFile);
        }
    }

    public function setUp()
    {
        $classname = static::getAdapterClassName();
        $this->adapter = $classname::newInstance();
    }

    public function testCreateNoFiles()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--create'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('-'))
            ->will($this->returnSelf());

        $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

        $mockProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(sprintf('--files-from %s', $nullFile)))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo((sprintf('> %s', self::$tarFile))))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->create(self::$tarFile, array());
    }

    public function testCreate()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--create'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', self::$tarFile)))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(__FILE__))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->create(self::$tarFile, array(__FILE__));

        return self::$tarFile;
    }

    /**
     * @depends testCreate
     */
    public function testOpen($tarFile)
    {
        $archive = $this->adapter->open($tarFile);
        $this->assertInstanceOf('Alchemy\Zippy\Archive\ArchiveInterface', $archive);

        return $archive;
    }

    /**
     * @depends testOpen
     */
    public function testListMembers($archive)
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--utc'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--list'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $archive->getLocation())))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->listMembers($archive->getLocation());
    }

    /**
     * @depends testOpen
     */
    public function testAddFile($archive)
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--delete'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--append'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $archive->getLocation())))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->add($archive->getLocation(), array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetVersion()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--version'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->getInflatorVersion();
    }

    /**
     * @depends testOpen
     */
    public function testExtract($archive)
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--extract'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $archive->getLocation())))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $dir = $this->adapter->extract($archive->getLocation());
        $pathinfo = pathinfo(self::$tarFile);
        $this->assertEquals($pathinfo['dirname'], $dir);
    }

    /**
     * @depends testOpen
     */
    public function testExtractWithExtractDirPrecised($archive)
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--extract'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--file=' . $archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo('--directory'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo(__DIR__))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(5))
            ->method('add')
            ->with($this->equalTo(__FILE__))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->extractMembers($archive->getLocation(), array(__FILE__), __DIR__);
    }

    /**
     * @depends testOpen
     */
    public function testRemoveMembers($archive)
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--delete'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--file=' . $archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(__DIR__ . '/../TestCase.php'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('path-to-file'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $archiveFileMock = $this->getMock('Alchemy\Zippy\Archive\MemberInterface');

        $archiveFileMock
            ->expects($this->any())
            ->method('getLocation')
            ->will($this->returnValue('path-to-file'));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->remove($archive->getLocation(), array(
            __DIR__ . '/../TestCase.php',
            $archiveFileMock
        ));
    }

    public function testThatGNUTarIsNotMarkedAsSupported()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->any())
            ->method('add')
            ->will($this->returnSelf());

        $process = $this->getSuccessFullMockProcess();

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($process));

        $process
            ->expects($this->once())
            ->method('getOutput')
            ->will($this->returnValue('tar (GNU tar) 1.17
Copyright (C) 2007 Free Software Foundation, Inc.
License GPLv2+: GNU GPL version 2 or later <http://gnu.org/licenses/gpl.html>
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Modified to support extended attributes.
Written by John Gilmore and Jay Fenlason.'));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->assertFalse($this->adapter->isSupported());
    }

    public function testThatBsdTarIsMarkedAsSupported()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->any())
            ->method('add')
            ->will($this->returnSelf());

        $process = $this->getSuccessFullMockProcess();

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($process));

        $process
            ->expects($this->once())
            ->method('getOutput')
            ->will($this->returnValue('bsdtar 2.8.3 - libarchive 2.8.3'));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->assertTrue($this->adapter->isSupported());
    }

    public function testGetName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals('bsd-tar', $classname::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals('bsdtar', $classname::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals('bsdtar', $classname::getDefaultDeflatorBinaryName());
    }

    abstract protected function getOptions();

    protected static function getAdapterClassName()
    {
        $this->fail(sprintf('Method %s should be implemented', __METHOD__));
    }

    private function getSuccessFullMockProcess()
    {
        $mockProcess = $this
            ->getMockBuilder('Symfony\Component\Process\Process')
            ->disableOriginalConstructor()
            ->getMock();

        $mockProcess
            ->expects($this->once())
            ->method('run');

        $mockProcess
            ->expects($this->once())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        return $mockProcess;
    }

    private function getZippyMockBuilder($mockedProcessBuilder)
    {
        $mockBuilder = $this->getMock('Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface');

        $mockBuilder
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($mockedProcessBuilder));

        return $mockBuilder;
    }
}
