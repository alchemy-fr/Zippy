<?php

namespace Alchemy\Zippy\Tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public static function getResourcesPath()
    {
        return __DIR__ . '/../../../resources';
    }

    public function getResource($expectedResource)
    {
        $resource = $this->getMock('Alchemy\Zippy\Adapter\Resource\ResourceInterface');
        $resource->expects($this->any())
            ->method('getResource')
            ->will($this->returnValue($expectedResource));

        return $resource;
    }
}
