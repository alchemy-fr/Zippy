<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Resource\ResourceUri;
use Traversable;

class PackageBuilder extends Package implements \IteratorAggregate
{
    /**
     * @var ResourceUri[]
     */
    private $resources = [];

    /**
     * @param ResourceUri $resource
     */
    public function addResource(ResourceUri $resource)
    {
        $this->resources[] = $resource;
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
        return new \ArrayIterator(array_merge(iterator_to_array(parent::getIterator()), $this->resources));
    }
}
