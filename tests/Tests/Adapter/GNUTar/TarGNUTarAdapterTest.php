<?php

namespace Alchemy\Zippy\Tests\Adapter\GNUTar;

use Alchemy\Zippy\Adapter\GNUTar\TarGNUTarAdapter;
use Alchemy\Zippy\Tests\Adapter\AdapterTestCase;
use Alchemy\Zippy\Parser\ParserFactory;

class TarGNUTarAdapterTest extends AdapterTestCase
{
    protected static $tarFile;

    /**
     * @var TarGNUTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), TarGNUTarAdapter::getName());

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

        $outputParser = ParserFactory::create(TarGNUTarAdapter::getName());

        $manager = $this->getResourceManagerMock(__DIR__);

        return new TarGNUTarAdapter($outputParser, $manager, $inflator, $inflator);
    }

    protected function provideSupportedAdapter()
    {
        $adapter = $this->provideAdapter();
        $this->setProbeIsOk($adapter);

        return $adapter;
    }

    protected function provideNotSupportedAdapter()
    {
        $adapter = $this->provideAdapter();
        $this->setProbeIsNotOk($adapter);

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

        $manager = $this->getResourceManagerMock(__DIR__, array('lalalalala'));
        $outputParser = ParserFactory::create(TarGNUTarAdapter::getName());
        $this->adapter = new TarGNUTarAdapter($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $this->getMockedProcessBuilderFactory($mockedProcessBuilder, 0));
        $this->setProbeIsOk($this->adapter);

        $this->adapter->create(self::$tarFile, array(__FILE__));

        $this->assertEquals(array(
            '-c',
            sprintf('--file=%s', $this->getExpectedAbsolutePathForTarget(self::$tarFile)),
            'lalalalala',
        ), $addedArguments->getArrayCopy());
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
            '--utc',
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
            '--overwrite',
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
        $this->assertEquals('gnu-tar', TarGNUTarAdapter::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $this->assertEquals(array('gnutar', 'tar'), TarGNUTarAdapter::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $this->assertEquals(array('gnutar', 'tar'), TarGNUTarAdapter::getDefaultDeflatorBinaryName());
    }
}
