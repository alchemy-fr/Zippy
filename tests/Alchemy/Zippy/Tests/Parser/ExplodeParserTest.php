<?php

namespace Alchemy\Zippy\Tests\Parser;

use Alchemy\Zippy\Parser\ExplodeParser;
use Alchemy\Zippy\Tests\AbstractTest;

class ExplodeParserTest extends AbstractTest
{
    public function testParse()
    {
        $parser = new ExplodeParser();
        $this->assertEquals(array('hellow', 'world'), $parser->parse("\nhellow\nworld\n"));
        $parser = new ExplodeParser(';');
        $this->assertEquals(array('hellow', 'world'), $parser->parse(";;hellow;world;;"));
    }
}
