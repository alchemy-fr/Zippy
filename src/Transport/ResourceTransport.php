<?php

namespace Alchemy\Zippy\Transport;

use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\ResourceWriter;

class ResourceTransport
{
    /**
     * @var ResourceReaderFactory
     */
    private $readerFactory;

    /**
     * @var ResourceWriter
     */
    private $resourceWriter;

    /**
     * @param ResourceReaderFactory $readerFactory
     * @param ResourceWriter $resourceWriter
     */
    public function __construct(
        ResourceReaderFactory $readerFactory,
        ResourceWriter $resourceWriter
    ) {
        $this->readerFactory = $readerFactory;
        $this->resourceWriter = $resourceWriter;
    }

    /**
     * Transports a source resource to a destination resource.
     *
     * @param ResourceUri $source The source file
     * @param ResourceUri $destination The target file
     */
    public function transport(ResourceUri $source, ResourceUri $destination)
    {
        $reader = $this->readerFactory->getReader($source);

        $this->resourceWriter->writeFromReader($reader, $destination);
    }
}
