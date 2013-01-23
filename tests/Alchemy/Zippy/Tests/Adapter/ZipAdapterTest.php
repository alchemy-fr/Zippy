<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\ZipAdapter;
use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Parser\ParserFactory;

class ZipAdapterTest extends TestCase
{
    protected static $zipFile;

    /**
     * @var ZipAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass()
    {
        self::$zipFile = sprintf('%s/%s.zip', self::getResourcesPath(), ZipAdapter::getName());

        if (file_exists(self::$zipFile)) {
            unlink(self::$zipFile);
        }
    }

    public static function tearDownAfterClass()
    {
        if (file_exists(self::$zipFile)) {
            unlink(self::$zipFile);
        }
    }

    public function setUp()
    {
        $inflator = $deflator = $this->getMockBuilder('Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory')
                                    ->disableOriginalConstructor()
                                    ->setMethods(array('useBinary'))
                                    ->getMock();

        $outputParser = ParserFactory::create(ZipAdapter::getName());

        $this->adapter = new ZipAdapter($outputParser, $inflator, $deflator);
    }

    /**
     * @expectedException Alchemy\Zippy\Exception\NotSupportedException
     */
    public function testCreateNoFiles()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->create(self::$zipFile, array());
    }

    public function testCreate()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-R'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo(self::$zipFile))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(__FILE__))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->create(self::$zipFile, array(__FILE__));

        return self::$zipFile;
    }

    /**
     * @depends testCreate
     */
    public function testOpen($zipFile)
    {
        $archive = $this->adapter->open($zipFile);
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
            ->with($this->equalTo('-l'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setDeflator($this->getZippyMockBuilder($mockProcessBuilder));

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
            ->with($this->equalTo('-R'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('-u'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo($archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->add($archive->getLocation(), array(__DIR__ . '/../TestCase.php'));
    }

    public function testgetInflatorVersion()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-h'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setParser($this->getMock('Alchemy\Zippy\Parser\ParserInterface'));
        $this->adapter->setInflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->getInflatorVersion();
    }

    public function testgetDeflatorVersion()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-h'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setParser($this->getMock('Alchemy\Zippy\Parser\ParserInterface'));
        $this->adapter->setDeflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->getDeflatorVersion();
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
            ->with($this->equalTo('-d'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo($archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(__DIR__ . '/../TestCase.php'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(3))
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

    /**
     * @depends testOpen
     */
    public function testExtract($archive)
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo($archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setDeflator($this->getZippyMockBuilder($mockProcessBuilder));

        $dir = $this->adapter->extract($archive->getLocation());
        $pathinfo = pathinfo(self::$zipFile);
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
            ->with($this->equalTo($archive->getLocation()))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo('-d'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo(__DIR__))
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

        $this->adapter->setDeflator($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->extractMembers($archive->getLocation(), array(__FILE__), __DIR__);
    }

    public function testGetName()
    {
        $this->assertEquals('zip', ZipAdapter::getName());
    }

    public function testGetDefaultInflatorBinaryName()
    {
        $this->assertEquals('zip', ZipAdapter::getDefaultInflatorBinaryName());
    }

    public function testGetDefaultDeflatorBinaryName()
    {
        $this->assertEquals('unzip', ZipAdapter::getDefaultDeflatorBinaryName());
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
