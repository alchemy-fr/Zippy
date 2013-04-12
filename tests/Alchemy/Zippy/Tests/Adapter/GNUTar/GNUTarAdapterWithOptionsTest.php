<?php

namespace Alchemy\Zippy\Tests\Adapter\GNUTar;

use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

abstract class GNUTarAdapterWithOptionsTest extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var AbstractGNUTarAdapter
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
        $this->adapter = $this->provideSupportedAdapter();
    }

    private function provideAdapter()
    {
        $classname = static::getAdapterClassName();

        $inflator = $this->getMockBuilder('Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                ->disableOriginalConstructor()
                ->setMethods(array('useBinary'))
                ->getMock();

        $outputParser = ParserFactory::create($classname::getName());

        $manager = $this->getResourceManagerMock(__DIR__);

        return new $classname($outputParser, $manager, $inflator);
    }

    protected function provideNotSupportedAdapter()
    {
        $adapter = $this->provideAdapter();
        $this->setProbeIsNotOk($adapter);

        return $adapter;
    }

    protected function provideSupportedAdapter()
    {
        $adapter = $this->provideAdapter();
        $this->setProbeIsOk($adapter);

        return $adapter;
    }

    public function testCreateNoFiles()
    {
        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--create'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('-'))
            ->will($this->returnSelf());

        $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

        $mockedProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(sprintf('--files-from %s', $nullFile)))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo((sprintf('> %s', self::$tarFile))))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->create(self::$tarFile, array());
    }

    public function testCreate()
    {
        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--create'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', self::$tarFile)))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(3))
            ->method('setWorkingDirectory')
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('lalalalala'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $classname = static::getAdapterClassName();
        $outputParser = ParserFactory::create($classname::getName());
        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));

        $this->adapter = new $classname($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder));
        $this->setProbeIsOk($this->adapter);

        $this->adapter->create(self::$tarFile, array(__FILE__));
    }

    public function testOpen()
    {
        $archive = $this->adapter->open($this->getResource(self::$tarFile));
        $this->assertInstanceOf('Alchemy\Zippy\Archive\ArchiveInterface', $archive);

        return $archive;
    }

    public function testListMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--utc'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--list'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('-v'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $resource->getResource())))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->listMembers($resource);
    }

    public function testAddFile()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--delete'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--append'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $resource->getResource())))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->add($resource, array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetVersion()
    {
        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--version'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getInflatorVersion();
    }

    public function testExtract()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--extract'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $resource->getResource())))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('--overwrite-dir'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo('--overwrite'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $dir = $this->adapter->extract($resource);
        $pathinfo = pathinfo(self::$tarFile);
        $this->assertEquals($pathinfo['dirname'], $dir->getPath());
    }

    public function testExtractWithExtractDirPrecised()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--extract'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--file=' . $resource->getResource()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('--overwrite-dir'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo('--overwrite'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(5))
            ->method('add')
            ->with($this->equalTo('--directory'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(6))
            ->method('add')
            ->with($this->equalTo(__DIR__))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(7))
            ->method('add')
            ->with($this->equalTo(__FILE__))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->extractMembers($resource, array(__FILE__), __DIR__);
    }

    public function testRemoveMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockedProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--delete'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--file=' . $resource->getResource()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(__DIR__ . '/../TestCase.php'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('path-to-file'))
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $archiveFileMock = $this->getMock('Alchemy\Zippy\Archive\MemberInterface');

        $archiveFileMock
            ->expects($this->any())
            ->method('getLocation')
            ->will($this->returnValue('path-to-file'));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->remove($resource, array(
            __DIR__ . '/../TestCase.php',
            $archiveFileMock
        ));
    }

    public function testGetName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals('gnu-tar', $classname::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals(array('gnutar', 'tar'), $classname::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals(array('gnutar', 'tar'), $classname::getDefaultDeflatorBinaryName());
    }

    abstract protected function getOptions();

    protected static function getAdapterClassName()
    {
        $this->fail(sprintf('Method %s should be implemented', __METHOD__));
    }
}
