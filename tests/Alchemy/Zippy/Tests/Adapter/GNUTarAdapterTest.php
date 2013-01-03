<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Adapter\GNUTarAdapter;
use Alchemy\Zippy\Parser\GNUTarOutputParser;
use Alchemy\Zippy\Tests\AbstractTest;

class GNUTarAdapterTest extends AbstractTest
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
        $this->adapter = new GNUTarAdapter(new GNUTarOutputParser());
        
        if (!$this->adapter->isSupported()) {
            $this->markTestSkipped(sprintf('`%s` is not supported', $this->adapter->getDefaultBinaryName()));
        }
    }
    
    /**
     * @expectedException  Alchemy\Zippy\Exception\RuntimeException
     */
    public function testCreateFailed()
    {
        $this->adapter->create(self::$tarFile, array());
    }
    
    public function testCreate()
    {
        $this->adapter->create(self::$tarFile, array(__FILE__, __DIR__ . '/AbstractAdapterBinaryTest.php'));
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
        $this->assertEquals(2, count($members));
    }
    
    /**
     * @depends testOpen
     */
    public function testAddFile($archive)
    {
        $fileIterator = $this->adapter->addFile($archive->getLocation(), array(__DIR__ . '/../AbstractTest.php'));
        
        $this->assertEquals(1, count($fileIterator));
        $this->assertEquals(3, count($archive->members()));
    }
    
    public function testgetVersion()
    {
        $version = $this->adapter->getVersion();
        $this->assertTrue(is_string($version));
    }
    
    public function testGetName()
    {
        $this->assertEquals('gnu-tar', $this->adapter->getName());
    }
    
    public function testGetDefaultBinaryName()
    {
        $this->assertEquals('tar', $this->adapter->getDefaultBinaryName());
    }
}
