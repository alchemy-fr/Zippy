<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\PathUtil;

class PathUtilTest extends TestCase
{
    /**
     * @dataProvider providePathData
     */
    public function testBasename($expected, $context)
    {
        $this->assertEquals($expected, PathUtil::basename($context));
    }

    public function providePathData()
    {
        return array(
            array('file.ext', 'input/path/to/local/file.ext'),
            array('file.ext', 'input\path\to\local\file.ext'),
            array('file.ext', '\file.ext'),
            array('file.ext', 'file.ext'),
            array('Ängelholm.jpg', '/tmp/Ängelholm.jpg'),
            array('Ängelholm.jpg', '\tmp\Ängelholm.jpg'),
            array('Ängelholm.jpg', '\Ängelholm.jpg'),
            array('Ängelholm.jpg', 'Ängelholm.jpg'),
            array('я-utf8-name.jpg', '/tmp/я-utf8-name.jpg'),
            array('я-utf8-name.jpg', '\tmp\я-utf8-name.jpg'),
            array('я-utf8-name.jpg', 'я-utf8-name.jpg'),
            array('я-utf8-name.jpg', '/я-utf8-name.jpg'),
            array('logo.png', 'http://google.com/tmp/logo.png'),
            array('Ängelholm.png', 'http://google.com/city/Ängelholm.png'),
            array('Ängelholm.png', 'http://google.com/я/Ängelholm.png')
        );
    }
}
