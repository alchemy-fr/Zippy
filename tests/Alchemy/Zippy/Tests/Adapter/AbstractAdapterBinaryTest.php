<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Tests\AbstractTestFramework;

class AbstractAdapterBinaryTest extends AbstractTestFramework
{
    protected $abstractMock;
    
    public function setUp()
    {
        $this->abstractMock = $this->getMockForAbstractClass('Alchemy\Zippy\Adapter\AbstractBinaryAdapter');
        
        $mock = $this->getMock('Alchemy\Zippy\ProcessBuilder\ProcessBuilderInterface');
        
        $this->abstractMock->expects($this->any())
             ->method('getProcessBuilder')
             ->will($this->returnValue($mock));
    }
    /**
     * @expectedException Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testUseBinaryNonExecutable()
    {
        $this->abstractMock->useBinary(__FILE__);
    }
}
