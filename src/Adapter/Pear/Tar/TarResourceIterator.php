<?php

namespace Alchemy\Zippy\Adapter\Pear\Tar;

use Alchemy\Zippy\Package\Iterator\AbstractIterator;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Resource\Reader\StringReader;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

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
        $current = $this->getIterator()->current();
        $resource = ResourceUri::fromString($current['filename']);

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
        return new \ArrayIterator($this->archive->listContent());
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
