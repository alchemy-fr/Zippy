<?php

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

use Alchemy\Zippy\Adapter\BSDTar\TarBSDTarAdapter;
use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

class TarBSDTarAdapterTest extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var TarBSDTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), TarBSDTarAdapter::getName());

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
        $inflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                ->disableOriginalConstructor()
                ->setMethods(array('useBinary'))
                ->getMock();

        $outputParser = ParserFactory::create(TarBSDTarAdapter::getName());

        $manager = $this->getResourceManagerMock(__DIR__);

        return new TarBSDTarAdapter($outputParser, $manager, $inflator, $inflator);
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
            '-f',
            $this->getExpectedAbsolutePathForTarget(self::$tarFile),
            '-T',
            $nullFile,
        ), $addedArguments->getArrayCopy());
    }

    public function testCreate()
    {
        $outputParser = ParserFactory::create(TarBSDTarAdapter::getName());
        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));
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

        $this->adapter = new TarBSDTarAdapter($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $this->getMockedProcessBuilderFactory($mockedProcessBuilder, 0));
        $this->setProbeIsOk($this->adapter);
        $this->adapter->create(self::$tarFile, array(__FILE__));

        $this->assertEquals(array(
            '-c',
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
        ), $addedArguments->getArrayCopy());
    }

    public function testAddFile()
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

        $this->adapter->add($resource, array(__DIR__ . '/../TestCase.php'));

        $this->assertEquals(array(
            '--append',
            sprintf('--file=%s', $resource->getResource()),
        ), $addedArguments->getArrayCopy());
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

        $this->assertEquals(array(
            '--version',
        ), $addedArguments->getArrayCopy());
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
            __DIR__ . '/../TestCase.php',
            'path-to-file',
        ), $addedArguments->getArrayCopy());
    }

    public function testGetName()
    {
        $this->assertEquals('bsd-tar', TarBSDTarAdapter::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $this->assertEquals(array('bsdtar', 'tar'), TarBSDTarAdapter::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $this->assertEquals(array('bsdtar', 'tar'), TarBSDTarAdapter::getDefaultDeflatorBinaryName());
    }
}
