<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Resource\ResourceUri;

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
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_merge(iterator_to_array(parent::getIterator()), $this->resources));
    }
}
