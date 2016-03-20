<?php
namespace Alchemy\Zippy\Resource;

use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\ResourceWriter;

interface ResourceWriterResolver
{
    /**
     * Resolves a writer for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceWriter
     */
    public function resolveWriter(ResourceUri $resource);
}
