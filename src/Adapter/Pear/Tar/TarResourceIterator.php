<?php

namespace Alchemy\Zippy\Adapter\Pear\Tar;

use Alchemy\Zippy\Iterator\MappingArrayIterator;
use Alchemy\Zippy\Package\Iterator\AbstractIterator;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Resource\Reader\StringReader;
use Alchemy\Resource\ResourceReader;
use Alchemy\Resource\ResourceReaderResolver;
use Alchemy\Resource\ResourceUri;

class TarResourceIterator extends AbstractIterator implements ResourceReaderResolver
{
    /**
     * @var \Archive_Tar
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
        $this->archive = new \Archive_Tar($container->getRelativeUri()->getResource());
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
     * @return \ArrayIterator
     */
    protected function buildIterator()
    {
        return new MappingArrayIterator($this->archive->listContent(), function ($current) {
            return ResourceUri::fromString($current['filename']);
        });
    }

    /**
     * Resolves a reader for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function resolveReader(ResourceUri $resource)
    {
        return new StringReader($this->archive->extractInString($resource->getResource()));
    }
}
