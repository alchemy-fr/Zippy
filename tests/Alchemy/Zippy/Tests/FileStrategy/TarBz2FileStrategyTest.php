<?php

namespace Alchemy\Zippy\Tests\FileStrategy;


use Alchemy\Zippy\FileStrategy\TarBz2FileStrategy;

class TarBz2FileStrategyTest extends FileStrategyTestCase
{
    protected function getStrategy($container)
    {
        return new TarBz2FileStrategy($container);
    }
}
