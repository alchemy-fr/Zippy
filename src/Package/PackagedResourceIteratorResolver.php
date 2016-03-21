<?php

namespace Alchemy\Zippy\Package;

interface PackagedResourceIteratorResolver
{
    public function resolveIterator(PackagedResource $container);
}
