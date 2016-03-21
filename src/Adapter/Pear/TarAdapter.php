<?php

namespace Alchemy\Zippy\Adapter\Pear;

use Alchemy\Zippy\Adapter\Adapter;
use Alchemy\Zippy\Adapter\Pear\Tar\TarResourceIterator;
use Alchemy\Zippy\Package\Resolver\ProtocolBasedIteratorResolver;
use Alchemy\Resource\Resolver\ProtocolBasedReaderResolver;
use Alchemy\Resource\Resolver\ProtocolBasedWriterResolver;

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
