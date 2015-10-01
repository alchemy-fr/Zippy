<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\FileStrategy\TB2FileStrategy;

class TB2FileStrategyTest extends FileStrategyTestCase
{
    protected function getStrategy($container)
    {
        return new TB2FileStrategy($container);
    }
}
