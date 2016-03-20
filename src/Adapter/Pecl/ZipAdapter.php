<?php

namespace Alchemy\Zippy\Adapter\Pecl;

use Alchemy\Zippy\Adapter\Adapter;
use Alchemy\Zippy\Adapter\Pecl\Zip\ZipResourceIterator;
use Alchemy\Zippy\Package\IteratorResolver\ProtocolBasedIteratorResolver;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;

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
