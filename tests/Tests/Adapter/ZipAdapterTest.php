<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\ZipAdapter;
use Alchemy\Zippy\Parser\ParserFactory;

class ZipAdapterTest extends AdapterTestCase
{
    protected static $zipFile;

    /**
     * @var ZipAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass(): void
    {
        self::$zipFile = sprintf('%s/%s.zip', self::getResourcesPath(), ZipAdapter::getName());

        if (file_exists(self::$zipFile)) {
            unlink(self::$zipFile);
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$zipFile)) {
            unlink(self::$zipFile);
        }
    }

    public function setUp(): void
    {
        $this->adapter = $this->provideSupportedAdapter();
    }

    protected function provideNotSupportedAdapter()
    {
        $inflator = $deflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->setMethods(array('useBinary'))
                                    ->getMock();

        $outputParser = ParserFactory::create(ZipAdapter::getName());

        $adapter = new ZipAdapter($outputParser, $this->getResourceManagerMock(), $inflator, $deflator);
        $this->setProbeIsNotOk($adapter);

        return $adapter;
    }

    protected function provideSupportedAdapter()
    {
        $inflator = $deflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->setMethods(array('useBinary'))
                                    ->getMock();

        $outputParser = ParserFactory::create(ZipAdapter::getName());

        $adapter = new ZipAdapter($outputParser, $this->getResourceManagerMock(), $inflator, $deflator);
        $this->setProbeIsOk($adapter);

        return $adapter;
    }

    public function testCreateNoFiles()
    {
        $this->expectException(\Alchemy\Zippy\Exception\NotSupportedException::class);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->create(self::$zipFile, array());
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

        $manager = $this->getResourceManagerMock(__DIR__, array('lalala'));
        $outputParser = ParserFactory::create(ZipAdapter::getName());
        $deflator = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->setMethods(array('useBinary'))
                                    ->getMock();

        $this->adapter = new ZipAdapter($outputParser, $manager, $this->getMockedProcessBuilderFactory($mockedProcessBuilder), $deflator);
        $this->setProbeIsOk($this->adapter);

        $this->adapter->create(self::$zipFile, array(__FILE__));

        $this->assertEquals(array(
            '-r',
            $this->getExpectedAbsolutePathForTarget(self::$zipFile),
            'lalala',
        ), $addedArguments->getArrayCopy());

        return self::$zipFile;
    }

    /**
     * @depends testCreate
     */
    public function testOpen($zipFile)
    {
        $archive = $this->adapter->open($this->getResource($zipFile));
        $this->assertInstanceOf('Alchemy\Zippy\Archive\ArchiveInterface', $archive);
    }

    public function testListMembers()
    {
        $resource = $this->getResource(self::$zipFile);
        $archive = $this->adapter->open($resource);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->listMembers($resource);

        $this->assertEquals(array('-l', $resource->getResource()), $addedArguments->getArrayCopy());
    }

    public function testAddFile()
    {
        $resource = $this->getResource(self::$zipFile);

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

        $this->assertEquals(array('-r', '-u', $resource->getResource()), $addedArguments->getArrayCopy());
    }

    public function testgetInflatorVersion()
    {
        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setParser($this->getMockBuilder('\Alchemy\Zippy\Parser\ParserInterface')->getMock());
        $this->adapter->setInflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getInflatorVersion();

        $this->assertEquals(array('-h'), $addedArguments->getArrayCopy());
    }

    public function testgetDeflatorVersion()
    {
        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setParser($this->getMockBuilder('\Alchemy\Zippy\Parser\ParserInterface')->getMock());
        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->getDeflatorVersion();

        $this->assertEquals(array('-h'), $addedArguments->getArrayCopy());
    }

    public function testRemoveMembers()
    {
        $resource = $this->getResource(self::$zipFile);

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
            '-d',
            $resource->getResource(),
            __DIR__ . '/../TestCase.php',
            'path-to-file',
        ), $addedArguments->getArrayCopy());
    }

    public function testExtract()
    {
        $resource = $this->getResource(self::$zipFile);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $dir = $this->adapter->extract($resource);

        $this->assertEquals(array('-o', $resource->getResource()), $addedArguments->getArrayCopy());

        $pathinfo = pathinfo(self::$zipFile);
        $this->assertEquals($pathinfo['dirname'], $dir->getPath());
    }

    public function testExtractWithExtractDirPrecised()
    {
        $resource = $this->getResource(self::$zipFile);

        $mockedProcessBuilder = $this->getMockBuilder('\Alchemy\Zippy\ProcessBuilder\ProcessBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $addedArguments = $this->recordAddedArguments($mockedProcessBuilder);

        $mockedProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setDeflator($this->getMockedProcessBuilderFactory($mockedProcessBuilder));

        $this->adapter->extractMembers($resource, array(__FILE__), __DIR__);

        $this->assertEquals(array(
            $resource->getResource(),
            '-d',
            __DIR__,
            __FILE__,
        ), $addedArguments->getArrayCopy());
    }

    public function testGetName()
    {
        $this->assertEquals('zip', ZipAdapter::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $this->assertEquals(array('zip'), ZipAdapter::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $this->assertEquals(array('unzip'), ZipAdapter::getDefaultDeflatorBinaryName());
    }
}
