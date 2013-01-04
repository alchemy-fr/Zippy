<?php

namespace Alchemy\Zippy\Tests\Parser;

use Alchemy\Zippy\Parser\GNUTarOutputParser;
use Alchemy\Zippy\Tests\AbstractTestFramework;

class GNUTarOutputParserTest extends AbstractTestFramework
{
    public function testParse()
    {
        $parser = new GNUTarOutputParser();
        $this->assertEquals(array('hellow', 'world'), $parser->parseFileListing("\nhellow\nworld\n"));
        $this->assertEquals('2.8.3', $parser->parseVersion("bsdtar 2.8.3 - libarchive 2.8.3"));
    }
}
