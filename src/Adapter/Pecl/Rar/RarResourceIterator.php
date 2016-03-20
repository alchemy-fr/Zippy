<?php

namespace Alchemy\Zippy\Adapter\Pecl\Rar;

use Alchemy\Zippy\Package\Iterator\AbstractIterator;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Resource\Reader\RawStreamReader;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class RarResourceIterator extends AbstractIterator implements ResourceReaderResolver
{
    /**
     * @var \RarArchive
     */
    private $archive;

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
        $this->archive = \RarArchive::open($container->getAbsoluteUri()->getResource());
    }

    /**
     * @return \Iterator
     */
    protected  function buildIterator()
    {
        return new \ArrayIterator($this->archive->getEntries());
    }

    /**
     * @return PackagedResource
     */
    public function current()
    {
        $current = $this->getIterator()->current();

        return new PackagedResource(
            ResourceUri::fromString($current->getName()),
            $this,
            $this->container->getWriterResolver(),
            $this->container
        );
    }

    /**
     * Resolves a reader for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function resolveReader(ResourceUri $resource)
    {
        return new RawStreamReader($this->archive->getEntry($resource->getResource())->getStream());
    }
}
