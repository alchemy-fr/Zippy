<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class PackagedResourceReaderResolver implements ResourceReaderResolver
{
    /**
     * @var PackagedResource
     */
    private $container;

    /**
     * @param PackagedResource $container
     */
    public function __construct(PackagedResource $container)
    {
        $this->container = $container;
    }

    /**
     * Resolves a reader for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function resolveReader(ResourceUri $resource)
    {
        $target = ResourceUri::fromProtocolAndResource(
            $this->container->getAbsoluteUri()->getProtocol(),
            $resource->getResource()
        );

        return $this->container->getReaderResolver()->resolveReader($target);
    }
}
