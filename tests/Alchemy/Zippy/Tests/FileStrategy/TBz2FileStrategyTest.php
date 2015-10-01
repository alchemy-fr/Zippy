<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\FileStrategy\TBz2FileStrategy;

class TBz2FileStrategyTest extends FileStrategyTestCase
{
    protected function getStrategy($container)
    {
        return new TBz2FileStrategy($container);
    }
}
