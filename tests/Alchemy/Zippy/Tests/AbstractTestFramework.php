<?php

namespace Alchemy\Zippy\Tests;

abstract class AbstractTestFramework extends \PHPUnit_Framework_TestCase
{
    public static function getResourcesPath()
    {
        return __DIR__ . '/../../../resources';
    }
}
