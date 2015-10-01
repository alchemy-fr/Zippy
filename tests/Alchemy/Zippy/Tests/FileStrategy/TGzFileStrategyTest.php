<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\FileStrategy\TGzFileStrategy;

class TGzFileStrategyTest extends FileStrategyTestCase
{
    protected function getStrategy($container)
    {
        return new TGzFileStrategy($container);
    }
}
