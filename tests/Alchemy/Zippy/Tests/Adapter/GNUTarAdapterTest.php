<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\GNUTarAdapter;
use Alchemy\Zippy\Parser\GNUTarOutputParser;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory;
use Alchemy\Zippy\Tests\AbstractTestFramework;

class GNUTarAdapterTest extends AbstractTestFramework
{
    protected static $tarFile;
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
    
    /**
     * @expectedException  Alchemy\Zippy\Exception\NotSupportedException
     */
    public function testCreateWithFilesFailed()
    {
        $this->adapter->create(self::$tarFile);
    }
    
    /**
     * @expectedException  Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testCreateNoFilesFailed()
    {
        $this->adapter->create(self::$tarFile, array());
    }
    
    public function testCreate()
    {
        $this->adapter->create(self::$tarFile, array(__FILE__));
        $this->assertFileExists(self::$tarFile);
        
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
        $members = $this->adapter->listMembers($archive->getLocation());
        
        $this->assertTrue(is_array($members));
        $this->assertEquals(1, count($members));
    }
    
    /**
     * @depends testOpen
     */
    public function testAddFile($archive)
    {
        $fileIterator = $this->adapter->addFile($archive->getLocation(), array(__DIR__ . '/../AbstractTestFramework.php'));
        
        $this->assertEquals(1, count($fileIterator));
        $this->assertEquals(2, count($archive->members()));
    }
    
    public function testgetVersion()
    {
        $version = $this->adapter->getVersion();
        $this->assertTrue(is_string($version));
    }
    
    public function testGetName()
    {
        $this->assertEquals('gnu-tar', GNUTarAdapter::getName());
    }
    
    public function testGetDefaultBinaryName()
    {
        $this->assertEquals('tar', GNUTarAdapter::getDefaultBinaryName());
    }
}
