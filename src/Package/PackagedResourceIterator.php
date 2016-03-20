<?php

namespace Alchemy\Zippy\Package;

interface PackagedResourceIterator extends \Iterator
{

    /**
     * @return PackagedResource
     */
    public function current();

    /**
     * @param PackagedResource $parent
     * @return PackagedResourceIterator
     */
    public function withParent(PackagedResource $parent);
}
