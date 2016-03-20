<?php

namespace Alchemy\Zippy\Resource;

class ResourceTransport
{
    /**
     * @var ResourceReaderResolver
     */
    private $readerResolver;

    /**
     * @var ResourceUri
     */
    private $source;

    /**
     * @var ResourceWriterResolver
     */
    private $writerResolver;

    /**
     * @param ResourceReaderResolver $readerResolver
     * @param ResourceWriterResolver $writerResolver
     * @param ResourceUri $source
     */
    public function __construct(
        ResourceReaderResolver $readerResolver,
        ResourceWriterResolver $writerResolver,
        ResourceUri $source
    ) {
        $this->readerResolver = $readerResolver;
        $this->writerResolver = $writerResolver;
        $this->source = $source;
    }

    /**
     * Transports a source resource to a destination resource.
     *
     * @param ResourceUri $destination The target file
     */
    public function write(ResourceUri $destination)
    {
        $reader = $this->readerResolver->resolveReader($this->source);
        $writer = $this->writerResolver->resolveWriter($destination);

        $writer->writeFromReader($reader, $destination);
    }
}
