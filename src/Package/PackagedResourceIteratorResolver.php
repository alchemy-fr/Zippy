<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Adapter\Pecl\Rar\RarResourceIterator;
use Alchemy\Zippy\Adapter\Pecl\Zip\ZipResourceIterator;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class PackagedResourceIteratorResolver
{

    public function resolveIterator(ResourceUri $resource, ResourceReaderResolver $readerResolver)
    {
        if ($resource->getProtocol() == 'rar') {
            return new RarResourceIterator($resource);
        }

        return new ZipResourceIterator($resource);
    }
}
