<?php

namespace Alchemy\Zippy\Tests\Resource\Teleporter;

use Alchemy\Zippy\Tests\TestCase;

class TeleporterTestCase extends TestCase
{
    public function provideContexts()
    {
        if (!is_dir(sys_get_temp_dir() . '/context-test')) {
            mkdir (sys_get_temp_dir() . '/context-test');
        }

        return array(
            array(sys_get_temp_dir()),
            array(sys_get_temp_dir() . '/context-test')
        );
    }

    public function provideNotExistingContexts()
    {
        return array(
            array(sys_get_temp_dir() . '/'.uniqid('zippy_teleporter'))
        );
    }
}
