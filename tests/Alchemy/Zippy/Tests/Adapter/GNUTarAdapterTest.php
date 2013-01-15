<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\GNUTarAdapter;
use Alchemy\Zippy\Tests\AbstractTestFramework;

class GNUTarAdapterTest extends AbstractTestFramework
{
    protected static $tarFile;

    /**
     * @var GNUTarAdapter
     */
    protected $adapter;

    public static function setUpBeforeClass()
    {
        self::$tarFile = sprintf('%s/%s.tar', self::getResourcesPath(), __CLASS__);

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
        $this->adapter = GNUTarAdapter::newInstance();

        if (!$this->adapter->isSupported()) {
            $this->markTestSkipped(sprintf('`%s` is not supported', $this->adapter->getDefaultBinaryName()));
        }
    }

    public function testCreateNoFilesFailed()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-cf'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo(self::$tarFile))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with($this->equalTo('/dev/null'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->will($this->returnValue($this->getSuccessFullMockProcess()));

        $this->adapter->setProcessBuilder($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->create(self::$tarFile, array());
    }

    public function testCreate()
    {
        $mockProcessBuilder = $this->getMock('Symfony\Component\Process\ProcessBuilder');

        $mockProcessBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with($this->equalTo('-cf'))
            ->will($this->returnSelf());

        $mockProcessBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with($this->equalTo(self::$tarFile))
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

        $this->adapter->setProcessBuilder($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->create(self::$tarFile, array(__FILE__));

        return self::$tarFile;
    }

    /**
     * @depends testCreate
     */
    public function testOpen($tarFile)
    {
        $archive = $this->adapter->open($tarFile);
        $this->assertInstanceOf('Alchemy\Zippy\ArchiveInterface', $archive);

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
            ->with($this->equalTo('-tf'))
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

        $this->adapter->setProcessBuilder($this->getZippyMockBuilder($mockProcessBuilder));

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
            ->with($this->equalTo('-rf'))
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

        $this->adapter->setProcessBuilder($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->add($archive->getLocation(), array(__DIR__ . '/../AbstractTestFramework.php'));
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

        $this->adapter->setProcessBuilder($this->getZippyMockBuilder($mockProcessBuilder));

        $this->adapter->getVersion();
    }

    public function testGetName()
    {
        $this->assertEquals('gnu-tar', GNUTarAdapter::getName());
    }

    public function testGetDefaultBinaryName()
    {
        $this->assertEquals('tar', GNUTarAdapter::getDefaultBinaryName());
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
