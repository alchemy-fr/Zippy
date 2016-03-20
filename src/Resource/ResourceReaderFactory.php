<?php

namespace Alchemy\Zippy\Resource;

interface ResourceReaderFactory
{

    /**
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function getReader(ResourceUri $resource);
}
