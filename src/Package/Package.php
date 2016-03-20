<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;
use Traversable;

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
     * @param PackagedResourceIteratorResolver $iteratorResolver
     */
    public function __construct(
        ResourceUri $container,
        ResourceReaderResolver $readerResolver,
        PackagedResourceIteratorResolver $iteratorResolver
    ) {
        $this->container = $container;
        $this->iteratorResolver = $iteratorResolver;

        parent::__construct($container, $readerResolver);
    }

    /**
     * @return ResourceUri
     */
    public function getContainer()
    {
        return clone $this->container;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->iteratorResolver
            ->resolveIterator($this->container, $this->getReaderResolver())
            ->withParent($this);
    }
}
