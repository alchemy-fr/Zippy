<?php

namespace Alchemy\Zippy\Tests\ProcessBuilder;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\ProcessBuilder\ProcessBuilderFactory;
use Symfony\Component\Filesystem\Filesystem;

class ProcessBuilderFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new ProcessBuilderFactory($this->getBinary());
        $this->assertInstanceOf('\Symfony\Component\Process\ProcessBuilder',$factory->create());
    }

    /**
     * @expectedException \Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testCreateFailed()
    {
        new ProcessBuilderFactory($this->getBinary(false));
    }

    public function testUseBinary()
    {
        $factory = new ProcessBuilderFactory($this->getBinary());
        $binary = $this->getBinary();
        $factory->useBinary($binary);
        $this->assertEquals($binary, $factory->getBinary());
    }

    /**
     * @expectedException \Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testUseBinaryFailed()
    {
        $factory = new ProcessBuilderFactory($this->getBinary());
        $factory->useBinary( $this->getBinary(false));
    }

    private function getBinary($exec = true)
    {
        $path = sys_get_temp_dir().'/'.uniqid('zippy_binary');
        if ($exec) {
            $fs = new Filesystem();
            $fs->touch($path);
            $fs->chmod($path, 0777);
        }

        return $path;
    }
}
