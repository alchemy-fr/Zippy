<?php

namespace Alchemy\Zippy\Tests\Parser;

use Alchemy\Zippy\Parser\ParserFactory;
use Alchemy\Zippy\Tests\TestCase;

class ParserFactoryTest extends TestCase
{
    /**
     * @dataProvider parserProvider
     */
    public function testFactory($type, $expected)
    {
        $parser = ParserFactory::create($type);
        $this->assertInstanceOf($expected, $parser);
    }

    public function parserProvider()
    {
        return array(
            array('gnu-tar','\Alchemy\Zippy\Parser\GNUTarOutputParser'),
            array('bsd-tar','\Alchemy\Zippy\Parser\BSDTarOutputParser'),
            array('zip','\Alchemy\Zippy\Parser\ZipOutputParser'),
        );
    }

    /**
     * @expectedException \Alchemy\Zippy\Exception\InvalidArgumentException
     */
    public function testInvalidType()
    {
        ParserFactory::create('');
    }
}
