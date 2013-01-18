<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\FileStrategy\ZipFileStrategy;

class ZipFileStrategyTest extends FileStrategyTestCase
{
    protected function getStrategy()
    {
        return new ZipFileStrategy($this->getContainer());
    }
}
