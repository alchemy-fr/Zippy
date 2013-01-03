<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Tests\AbstractTest;

class AbstractAdapterBinaryTest extends AbstractTest
{
    /**
     * @expectedException Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testUseBinaryNonExecutable()
    {
        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\Adapter\AbstractBinaryAdapter');
        
        $stub->useBinary(__FILE__);
    }
    
    public function testUseBinary()
    {
        $exeFile = sprintf('%s/executable_file', $this->getResourcesPath());
        
        touch($exeFile);
        chmod($exeFile, 0711);
        
        $stub = $this->getMockForAbstractClass('Alchemy\Zippy\Adapter\AbstractBinaryAdapter');
        $stub->useBinary($exeFile);
        
        $this->assertEquals($exeFile, $stub->getBinary());
        
        unlink($exeFile);
    }
}
