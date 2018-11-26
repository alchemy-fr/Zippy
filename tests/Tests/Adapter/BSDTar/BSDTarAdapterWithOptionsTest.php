<?php

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

use Alchemy\Zippy\Adapter\AbstractAdapter;
use Alchemy\Zippy\Adapter\AbstractTarAdapter;
use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

abstract class BSDTarAdapterWithOptionsTest extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var AbstractAdapter|AbstractTarAdapter
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

        $inflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
            ->disableOriginalConstructor()
            ->setMethods(array('useBinary'))
            ->getMock();

        $outputParser = ParserFactory::create($classname::getName());

        $manager = $this->getResourceManagerMock(__DIR__);

        return new $classname($outputParser, $manager, $inflator, $inflator);
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

    public function testNewinstance()
    {
        $classname = static::getAdapterClassName();
        $finder = $this->getMockBuilder('\Symfony\Component\Process\ExecutableFinder')
            ->disableOriginalConstructor()
            ->getMock();
        $manager = $this->getMockBuilder('\Alchemy\Zippy\Resource\ResourceManager')
            ->disableOriginalConstructor()
            ->getMock();

        $instance = $classname::newInstance(
            $finder,
            $manager,
            $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface')->getMock(),
            $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactoryInterface')->getMock()
        );

        $this->assertInstanceOf($classname, $instance);
    }

    public function testCreateNoFiles()
    {
        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-c'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('-f'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo($this->getExpectedAbsolutePathForTarget(self::$tarFile)))
            ->will($this->returnSelf());

        $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

        $mockedProcess
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('-T'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(5))
            ->method('add')
            ->with($this->equalTo($nullFile))
            ->will($this->returnSelf());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcess));

        $this->adapter->create(self::$tarFile, array());
    }

    public function testCreate()
    {
        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-c'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $this->getExpectedAbsolutePathForTarget(self::$tarFile))))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(3))
            ->method('setWorkingDirectory')
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('lalalalala'))
            ->will($this->returnSelf());

        $classname = static::getAdapterClassName();
        $outputParser = ParserFactory::create($classname::getName());
        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));

        $this->adapter = new $classname($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcess),
            $this->getMockedProcessBuilderFactory($mockedProcess, 0));
        $this->setProbeIsOk($this->adapter);

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

    public function testListMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--list'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('-v'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $resource->getResource())))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcess));

        $this->adapter->listMembers($resource);
    }

    public function testAddFile()
    {
        $resource = $this->getResource(self::$tarFile);
        $this->setExpectedException('Alchemy\Zippy\Exception\NotSupportedException',
            'Updating a compressed tar archive is not supported.');
        $this->adapter->add($resource, array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetVersion()
    {
        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--version'))
            ->will($this->returnSelf());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcess));

        $this->adapter->getInflatorVersion();
    }

    public function testExtract()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--extract'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo(sprintf('--file=%s', $resource->getResource())))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcess));

        $dir = $this->adapter->extract($resource);
        $pathinfo = pathinfo(self::$tarFile);
        $this->assertEquals($pathinfo['dirname'], $dir->getPath());
    }

    public function testExtractWithExtractDirPrecised()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-k'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--extract'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('--file=' . $resource->getResource()))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('--directory'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(5))
            ->method('add')
            ->with($this->equalTo(__DIR__))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(6))
            ->method('add')
            ->with($this->equalTo(__FILE__))
            ->will($this->returnSelf());

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcess));

        $this->adapter->extractMembers($resource, array(__FILE__), __DIR__);
    }

    public function testRemoveMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcess = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ZippyProcess')->setConstructorArgs(array(array()))->getMock();

        $mockedProcess
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('--delete'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('--file=' . $resource->getResource()))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo($this->getOptions()))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(3))
            ->method('add')
            ->with($this->equalTo(__DIR__ . '/../TestCase.php'))
            ->will($this->returnSelf());

        $mockedProcess
            ->expects($this->at(4))
            ->method('add')
            ->with($this->equalTo('path-to-file'))
            ->will($this->returnSelf());

        $archiveFileMock = $this->getMockBuilder('\Alchemy\Zippy\Archive\MemberInterface')->getMock();
        $archiveFileMock
            ->expects($this->any())
            ->method('getLocation')
            ->will($this->returnValue('path-to-file'));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcess));

        $this->adapter->remove($resource, array(
            __DIR__ . '/../TestCase.php',
            $archiveFileMock,
        ));
    }

    public function testGetName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals('bsd-tar', $classname::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals(array('bsdtar', 'tar'), $classname::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $classname = static::getAdapterClassName();
        $this->assertEquals(array('bsdtar', 'tar'), $classname::getDefaultDeflatorBinaryName());
    }

    abstract protected function getOptions();

    protected static function getAdapterClassName()
    {
        self::fail(sprintf('Method %s should be implemented', __METHOD__));
    }
}
