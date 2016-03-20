<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Package\IteratorResolver\ProtocolBasedIteratorResolver;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Resource\PathUtil;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\Writer\StreamWriter;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;

class FileAdapter implements Adapter
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

        $writerResolver->addWriter(new StreamWriter(), 'file');

        $iteratorResolver->addFactory('file', function (PackagedResource $container) use ($iteratorResolver) {
            $extension = PathUtil::extractExtension($container->getRelativeUri()->getResource());
            $factory = $iteratorResolver->getFactory($extension);

            return $factory($container);
        });

    }
}
