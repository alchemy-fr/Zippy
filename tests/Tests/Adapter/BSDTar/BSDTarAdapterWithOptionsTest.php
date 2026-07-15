<?php

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

abstract class BSDTarAdapterWithOptionsTest extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var AbstractBSDTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        $classname = static::getAdapterClassName();
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), $classname::getName());

        if (file_exists(self::$tarFile)) {
            unlink(self::$tarFile);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$tarFile)) {
            unlink(self::$tarFile);
        }
    }

    public function setUp(): void
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
        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $nullFile = defined('PHP_WINDOWS_VERSION_BUILD') ? 'NUL' : '/dev/null';

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->create(self::$tarFile, array());

        $this->assertEquals(array(
            '-c',
            $this->getOptions(),
            '-f',
            $this->getExpectedAbsolutePathForTarget(self::$tarFile),
            '-T',
            $nullFile,
        ), $addedArguments->getArrayCopy());
    }

    public function testCreate()
    {
        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->method('setWorkingDirectory')
            ->will($this->returnSelf());

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $classname = static::getAdapterClassName();
        $outputParser = ParserFactory::create($classname::getName());
        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));

        $this->adapter = new $classname($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $this->getMockedProcessBuilderFactory($mockedProcessBuilder, 0));
        $this->setProbeIsOk($this->adapter);

        $this->adapter->create(self::$tarFile, array(__FILE__));

        $this->assertEquals(array(
            '-c',
            $this->getOptions(),
            sprintf('--file=%s', $this->getExpectedAbsolutePathForTarget(self::$tarFile)),
            'lalalalala',
        ), $addedArguments->getArrayCopy());

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

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->listMembers($resource);

        $this->assertEquals(array(
            '--list',
            '-v',
            sprintf('--file=%s', $resource->getResource()),
            $this->getOptions(),
        ), $addedArguments->getArrayCopy());
    }

    public function testAddFile()
    {
        $resource = $this->getResource(self::$tarFile);
        $this->expectException('Alchemy\Zippy\Exception\NotSupportedException', 'Updating a compressed tar archive is not supported.');
        $this->adapter->add($resource, array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetVersion()
    {
        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getInflatorVersion();

        $this->assertEquals(array('--version'), $addedArguments->getArrayCopy());
    }

    public function testExtract()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $dir = $this->adapter->extract($resource);

        $this->assertEquals(array(
            '--extract',
            sprintf('--file=%s', $resource->getResource()),
            $this->getOptions(),
        ), $addedArguments->getArrayCopy());

        $pathinfo = pathinfo(self::$tarFile);
        $this->assertEquals($pathinfo['dirname'], $dir->getPath());
    }

    public function testExtractWithExtractDirPrecised()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->extractMembers($resource, array(__FILE__), __DIR__);

        $this->assertEquals(array(
            '-k',
            '--extract',
            '--file=' . $resource->getResource(),
            $this->getOptions(),
            '--directory',
            __DIR__,
            __FILE__,
        ), $addedArguments->getArrayCopy());
    }

    public function testRemoveMembers()
    {
        $resource = $this->getResource(self::$tarFile);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $archiveFileMock = $this->getMockBuilder('\Alchemy\Zippy\Archive\MemberInterface')->getMock();
        $archiveFileMock
            ->expects($this->any())
            ->method('getLocation')
            ->will($this->returnValue('path-to-file'));

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->remove($resource, array(
            __DIR__ . '/../TestCase.php',
            $archiveFileMock
        ));

        $this->assertEquals(array(
            '--delete',
            '--file=' . $resource->getResource(),
            $this->getOptions(),
            __DIR__ . '/../TestCase.php',
            'path-to-file',
        ), $addedArguments->getArrayCopy());
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
