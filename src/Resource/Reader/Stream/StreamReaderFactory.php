<?php

namespace Alchemy\Zippy\Resource\Reader\Stream;

use Alchemy\Zippy\Resource\Resource;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;

class StreamReaderFactory implements ResourceReaderFactory
{

    /**
     * @param \Alchemy\Zippy\Resource\Resource $resource
     * @param string $context
     * @return ResourceReader
     */
    public function getReader(Resource $resource, $context)
    {
        return new StreamReader($resource);
    }
}
