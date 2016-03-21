<?php

namespace Alchemy\Zippy\Adapter;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\Resolver\ProtocolBasedIteratorResolver;
use Alchemy\Resource\PathUtil;
use Alchemy\Resource\Resolver\ProtocolBasedReaderResolver;
use Alchemy\Resource\Resolver\ProtocolBasedWriterResolver;
use Alchemy\Resource\Writer\StreamWriter;

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
