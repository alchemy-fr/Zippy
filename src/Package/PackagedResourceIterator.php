<?php

namespace Alchemy\Zippy\Package;

interface PackagedResourceIterator extends \Iterator
{

    /**
     * @return PackagedResource
     */
    public function current();
}
