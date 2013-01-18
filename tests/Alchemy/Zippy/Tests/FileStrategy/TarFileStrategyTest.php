<?php

namespace Alchemy\Zippy\Tests\FileStrategy;

use Alchemy\Zippy\FileStrategy\TarFileStrategy;

class TarFileStrategyTest extends FileStrategyTestcase
{
    protected function getStrategy()
    {
        return new TarFileStrategy($this->getContainer());
    }
}
