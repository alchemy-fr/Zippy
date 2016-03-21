<?php

namespace Alchemy\Zippy\Resource\Reader;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Alchemy\Zippy\Resource\ResourceUri;

class StreamReaderFactory implements ResourceReaderFactory
{

    /**
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function createReaderFor(ResourceUri $resource)
    {
        return new StreamReader($resource);
    }
}
