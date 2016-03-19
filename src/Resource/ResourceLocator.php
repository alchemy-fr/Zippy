<?php

namespace Alchemy\Zippy\Resource;

class ResourceLocator
{
    public function mapResourcePath(Resource $resource, $context)
    {
        return rtrim($context, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $resource->getTarget();
    }
}
