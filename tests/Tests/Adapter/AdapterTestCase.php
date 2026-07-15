<?php

namespace Alchemy\Zippy\Tests\Adapter;

use Alchemy\Zippy\Tests\TestCase;

abstract class AdapterTestCase extends TestCase
{
    public function testIsSupported()
    {
        $adapter = $this->provideSupportedAdapter();
        $this->assertTrue($adapter->isSupported());
    }

    public function testIsNotSupported()
    {
        $adapter = $this->provideNotSupportedAdapter();
        $this->assertFalse($adapter->isSupported());
    }

    abstract protected function provideNotSupportedAdapter();

    abstract protected function provideSupportedAdapter();

    /**
     * Records, in call order, the arguments passed to the ProcessBuilder mock's
     * add() method. This replaces the deprecated at() matcher: assert the
     * returned ArrayObject once the tested action has run.
     *
     * @param \PHPUnit\Framework\MockObject\MockObject $mockedProcessBuilder
     *
     * @return \ArrayObject the recorded add() arguments, in order
     */
    protected function recordAddedArguments($mockedProcessBuilder)
    {
        $arguments = new \ArrayObject();

        $mockedProcessBuilder
            ->method('add')
            ->will($this->returnCallback(function ($argument) use ($arguments, $mockedProcessBuilder) {
                $arguments[] = $argument;

                return $mockedProcessBuilder;
            }));

        return $arguments;
    }
}
