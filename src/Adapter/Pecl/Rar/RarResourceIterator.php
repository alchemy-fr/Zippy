<?php

namespace Alchemy\Zippy\Adapter\Pecl\Rar;

use Alchemy\Zippy\Iterator\MappingArrayIterator;
use Alchemy\Zippy\Package\Iterator\AbstractIterator;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Resource\Reader\RawStreamReader;
use Alchemy\Resource\ResourceReader;
use Alchemy\Resource\ResourceReaderResolver;
use Alchemy\Resource\ResourceUri;

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
        return new MappingArrayIterator($this->archive->getEntries(), function ($current) {
            return ResourceUri::fromString($current->getName());
        });
    }

    /**
     * @return PackagedResource
     */
    public function current()
    {
        $resource = $this->getIterator()->current();

        return new PackagedResource(
            $resource,
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
