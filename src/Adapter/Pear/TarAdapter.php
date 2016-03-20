<?php

namespace Alchemy\Zippy\Adapter\Pear;

use Alchemy\Zippy\Adapter\Adapter;
use Alchemy\Zippy\Adapter\Pear\Tar\TarResourceIterator;
use Alchemy\Zippy\Package\IteratorResolver\ProtocolBasedIteratorResolver;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;

class TarAdapter implements Adapter
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
        $iteratorResolver->addFactory('tar', function ($container) {
            return new TarResourceIterator($container);
        });
    }
}
