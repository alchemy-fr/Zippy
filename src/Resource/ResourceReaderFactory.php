<?php

namespace Alchemy\Zippy\Resource;

interface ResourceReaderFactory
{

    /**
     * @param \Alchemy\Zippy\Resource\Resource $resource
     * @param string $context
     * @return ResourceReader
     */
    public function getReader(Resource $resource, $context);
}
