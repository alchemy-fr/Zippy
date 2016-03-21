<?php

namespace Alchemy\Zippy\Resource;

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
