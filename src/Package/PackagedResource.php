<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Resource\ResourceReader;
use Alchemy\Resource\ResourceReaderResolver;
use Alchemy\Resource\ResourceUri;
use Alchemy\Resource\ResourceTransport;
use Alchemy\Resource\ResourceWriterResolver;

class PackagedResource
{
    /**
     * @var PackagedResource
     */
    private $parent;

    /**
     * @var ResourceUri
     */
    private $relativeUri;

    /**
     * @var ResourceReaderResolver
     */
    private $readerResolver;

    /**
     * @var ResourceWriterResolver
     */
    private $writerResolver;

    /**
     * @param ResourceUri $resourceUri
     * @param ResourceReaderResolver $readerResolver
     * @param ResourceWriterResolver $writerResolver
     * @param PackagedResource $parent
     */
    public function __construct(
        ResourceUri $resourceUri,
        ResourceReaderResolver $readerResolver,
        ResourceWriterResolver $writerResolver,
        PackagedResource $parent = null
    ) {
        $this->relativeUri = $resourceUri;
        $this->parent = $parent;
        $this->readerResolver = $readerResolver;
        $this->writerResolver = $writerResolver;
    }

    /**
     * @return ResourceUri
     */
    public function getRelativeUri()
    {
        return $this->relativeUri;
    }

    /**
     * @return ResourceReader
     */
    public function getReader()
    {
        return $this->readerResolver->resolveReader($this->getRelativeUri());
    }

    /**
     * @return ResourceTransport
     */
    public function getTransport()
    {
        return new ResourceTransport($this->readerResolver, $this->writerResolver, $this->getRelativeUri());
    }

    /**
     * @return ResourceReaderResolver
     */
    public function getReaderResolver()
    {
        return $this->readerResolver;
    }

    /**
     * @return ResourceWriterResolver
     */
    public function getWriterResolver()
    {
        return $this->writerResolver;
    }

    /**
     * return ResourceUri
     */
    public function getAbsoluteUri()
    {
        $uri = $this->relativeUri->getUri();

        if ($this->parent && $this->parent->isRoot()) {
            $uri = $this->parent->getAbsoluteUri() . '#/' . $this->relativeUri->getResource();
        }

        return new ResourceUri($uri);
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->parent == null;
    }
}
