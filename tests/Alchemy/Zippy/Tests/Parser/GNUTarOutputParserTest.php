<?php

namespace Alchemy\Zippy\Tests\Parser;

use Alchemy\Zippy\Parser\GNUTarOutputParser;
use Alchemy\Zippy\Tests\AbstractTestFramework;
use Alchemy\Zippy\MemberInterface;

class GNUTarOutputParserTest extends AbstractTestFramework
{
    public function testNewParser()
    {
        return new GNUTarOutputParser();
    }

    /**
     * @depends testNewParser
     */
    public function testParseFileListing($parser)
    {
        $output = "drwxrwxrwx myself/user 0 May 31 21:49 1990 practice/
            -rw-rw-rw- myself/user 42 May 21 13:29 1990 practice/blues
            -rw-rw-rw- myself/user 62 May 23 10:55 1990 practice/folk
            -rw-rw-rw- myself/user 40 May 21 13:30 1990 practice/jazz
            -rw-rw-rw- myself/user 10240 May 31 21:48 1990 practice/records";

        $members = $parser->parseFileListing($output);

        $this->assertEquals(5, count($members));

        foreach ($members as $member) {
            $this->assertTrue($member instanceof MemberInterface);
        }

        $memberDirectory = array_shift($members);

        $this->assertTrue($memberDirectory->isDir());
        $this->assertEquals('practice/', $memberDirectory->getLocation());
        $this->assertEquals(0, $memberDirectory->getSize());
        $date = $memberDirectory->getLastModifiedDate();
        $this->assertTrue($date instanceof \DateTime);
        $this->assertEquals('644183340', $date->format("U"));

        $memberFile = array_pop($members);

        $this->assertFalse($memberFile->isDir());
        $this->assertEquals('practice/records', $memberFile->getLocation());
        $this->assertEquals(10240, $memberFile->getSize());
        $date = $memberFile->getLastModifiedDate();
        $this->assertTrue($date instanceof \DateTime);
        $this->assertEquals('644183280', $date->format("U"));
    }

    /**
     * @depends testNewParser
     */
    public function testParseVersion($parser)
    {
        $this->assertEquals('2.8.3', $parser->parseVersion("bsdtar 2.8.3 - libarchive 2.8.3"));
    }
}
