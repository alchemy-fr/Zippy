<?php

namespace Alchemy\Zippy\Adapter\Pecl\Rar;

use Alchemy\Zippy\Package\Iterator\AbstractIterator;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Resource\ResourceUri;

class RarResourceIterator extends AbstractIterator
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

        $this->readerResolver = new RarResourceReaderResolver($this->archive);
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
            $this->readerResolver,
            $this->container->getWriterResolver(),
            $this->container
        );
    }
}
