<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Package\Iterator\FilteredPackagedResourceIterator;
use Alchemy\Resource\ResourceReaderResolver;
use Alchemy\Resource\ResourceUri;
use Alchemy\Resource\ResourceWriterResolver;

class Package extends PackagedResource implements \IteratorAggregate
{
    /**
     * @var ResourceUri
     */
    private $container;

    /**
     * @var PackagedResourceIteratorResolver
     */
    private $iteratorResolver;

    /**
     * @param ResourceUri $container
     * @param ResourceReaderResolver $readerResolver
     * @param ResourceWriterResolver $writerResolver
     * @param PackagedResourceIteratorResolver $iteratorResolver
     */
    public function __construct(
        ResourceUri $container,
        ResourceReaderResolver $readerResolver,
        ResourceWriterResolver $writerResolver,
        PackagedResourceIteratorResolver $iteratorResolver
    ) {
        $this->container = $container;
        $this->iteratorResolver = $iteratorResolver;

        parent::__construct($container, $readerResolver, $writerResolver);
    }

    /**
     * @return ResourceUri
     */
    public function getContainer()
    {
        return clone $this->container;
    }

    /**
     * @return PackagedResourceIterator
     */
    public function getIterator()
    {
        return $this->iteratorResolver->resolveIterator($this);
    }

    /**
     * @param callable $filter
     * @return FilteredPackagedResourceIterator
     */
    public function filter(callable $filter)
    {
        return new FilteredPackagedResourceIterator($this->getIterator(), $filter);
    }
}
