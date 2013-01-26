<?php

namespace Alchemy\Zippy\Tests\ResourceManager;

use Alchemy\Zippy\Tests\TestCase;
use Alchemy\Zippy\Resource\ResourceManager;

class ResourceManagerTest extends TestCase
{
    public function testFetch()
    {
        $wd = '/working/directory';

        $arguments = array(
             $wd . '/path/to/local/file.ext',
             $wd . '/path/to/a/../local/file2.ext',
             '/absolute/path/to/local/file.ext',
             '/absolute/path/to/a/../local/file2.ext',
            'http://www.path-to.img/external-file.ext',
            'http://www.path-to.img/directory/external-file2.ext',
            'file:///path/to/file-file.ext',
            'file://'.$wd. '/path/to/a/../local/file3.ext',
            '/I/want/this/file/to/go/there' => 'file://path/to/file-file2.ext'
        );

        $resourceManger = new ResourceManager();

        $final = $resourceManger->handle($wd, $arguments);

        $expected = array(
            'path/to/local/file.ext',
            'path/to/local/fil2.ext',
            'file.ext',
            'fil2.ext',
            'external-file.ext',
            'external-file2.ext',
            'file-file.ext',
            'path/to/local/fil3.ext',
            'I/want/this/file/to/go/there/file-file2.ext'
        );
    }
}
