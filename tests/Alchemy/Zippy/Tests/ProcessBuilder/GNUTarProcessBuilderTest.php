<?php

namespace Alchemy\Zippy\Tests\ProcessBuilder;

use Alchemy\Zippy\ProcessBuilder\GNUTarProcessBuilder;
use Alchemy\Zippy\Tests\AbstractTestFramework;

class GNUTarProcessBuilderTest extends AbstractTestFramework
{
    /**
     * @var GNUTarProcessBuilder 
     */
    protected $processBuilder;
    
    /**
     * @var String
     */
    protected $binary;
    
    public function setUp()
    {
        $this->binary = '/usr/bin/tar';
        
        $mock = $this->getMock('Symfony\Component\Process\ExecutableFinder');
        
        $mock->expects($this->any())
             ->method('find')
             ->will($this->returnValue($this->binary));
        
        $this->processBuilder = new GNUTarProcessBuilder($this->binary, $mock);
        
        unset($mock);
    }
    
    /**
     * @expectedException Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testGetAddNoExistingFileProcess()
    {
        $this->processBuilder->getAddFileProcess('/home/john/doe.tar', array(
            'lorem', 'ipsum'
        ));
    }
    
    public function testGetAddFileProcess()
    {
        $process = $this->processBuilder->getAddFileProcess('/home/john/doe.tar', array(
            __FILE__
        ));
        
        $expected = sprintf("'/usr/bin/tar' '-rf' '/home/john/doe.tar' '%s'", __FILE__);
        
        $this->assertEquals($expected, $process->getCommandLine());
        
        unset($process);
    }
    
    /**
     * @expectedException Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testGetCreateNoExistingFileArchiveProcess()
    {
        $this->processBuilder->getCreateArchiveProcess('/home/john/doe.tar', array(
           'lorem', 'ipsum'
        ));
    }
    
    public function testGetCreateArchiveProcess()
    {
        $process = $this->processBuilder->getCreateArchiveProcess('/home/john/doe.tar', array(
            __FILE__
        ));
        
        $expected = sprintf("'/usr/bin/tar' '-cf' '/home/john/doe.tar' '%s'", __FILE__);
        
        $this->assertEquals($expected, $process->getCommandLine());
        
        unset($process);
    }
    
    public function testGetListMembersProcess()
    {
        $process = $this->processBuilder->getListMembersProcess('/home/john/doe.tar');
        
        $expected = "'/usr/bin/tar' '-tf' '/home/john/doe.tar'";
        
        $this->assertEquals($expected, $process->getCommandLine());
        
        unset($process);
    }
    
    public function testGetVersionProcess()
    {
        $process = $this->processBuilder->getVersionProcess();
        
        $expected = "'/usr/bin/tar' '--version'";
        
        $this->assertEquals($expected, $process->getCommandLine());
        
        unset($process);
    }
    
    public function testGetHelpProcess()
    {
        $process = $this->processBuilder->getHelpProcess();
        
        $expected = "'/usr/bin/tar' '-h'";
        
        $this->assertEquals($expected, $process->getCommandLine());
        
        unset($process);
    }
}
