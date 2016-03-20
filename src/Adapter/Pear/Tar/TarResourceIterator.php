<?php

namespace Alchemy\Zippy\Adapter\Pear\Tar;

use Alchemy\Zippy\Package\Iterator\AbstractIterator;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Resource\ResourceUri;

class TarResourceIterator extends AbstractIterator
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
     * @var TarResourceReaderResolver
     */
    private $readerResolver;

    public function __construct(PackagedResource $container)
    {
        $this->container = $container;
        $this->archive = new \Archive_Tar($container->getRelativeUri()->getResource());

        $this->readerResolver = new TarResourceReaderResolver($this->archive);
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
            $this->readerResolver,
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
}
