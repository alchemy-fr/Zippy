<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Package\Resolver\ProtocolBasedIteratorResolver;
use Alchemy\Zippy\Resource\Resolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\Resolver\ProtocolBasedWriterResolver;

interface Adapter
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
    );
}
