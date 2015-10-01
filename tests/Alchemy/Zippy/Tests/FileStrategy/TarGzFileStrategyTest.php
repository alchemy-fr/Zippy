<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\FileStrategy\TarGzFileStrategy;

class TarGzFileStrategyTest extends FileStrategyTestCase
{
    protected function getStrategy($container)
    {
        return new TarGzFileStrategy($container);
    }
}
