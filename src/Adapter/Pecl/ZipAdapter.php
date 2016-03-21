<?php

namespace Alchemy\Zippy\Adapter\Pecl;

use Alchemy\Zippy\Adapter\Adapter;
use Alchemy\Zippy\Adapter\Pecl\Zip\ZipResourceIterator;
use Alchemy\Zippy\Package\Resolver\ProtocolBasedIteratorResolver;
use Alchemy\Resource\Resolver\ProtocolBasedReaderResolver;
use Alchemy\Resource\Resolver\ProtocolBasedWriterResolver;

class ZipAdapter implements Adapter
{

    /**
     * @param ProtocolBasedIteratorResolver $iteratorResolver
     * @param ProtocolBasedReaderResolver $readerResolver
     * @param ProtocolBasedWriterResolver $writerResolver
     */
    public function registerProtocols(
        ProtocolBasedIteratorResolver $iteratorResolver,
        ProtocolBasedReaderResolver $readerResolver,
        ProtocolBasedWriterResolver $writerResolver
    ) {
        $iteratorResolver->addFactory('zip', function ($container) {
            return new ZipResourceIterator($container);
        });
    }
}
