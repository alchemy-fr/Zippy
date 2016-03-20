<?php

namespace Alchemy\Zippy\Package;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

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
     * @param ResourceUri $resourceUri
     * @param ResourceReaderResolver $readerResolver
     * @param PackagedResource $parent
     */
    public function __construct(
        ResourceUri $resourceUri,
        ResourceReaderResolver $readerResolver,
        PackagedResource $parent = null
    ) {
        $this->relativeUri = $resourceUri;
        $this->parent = $parent;
        $this->readerResolver = $readerResolver;
    }

    /**
     * @param PackagedResource $parent
     * @return PackagedResource
     */
    public function withParent(PackagedResource $parent)
    {
        return new self($this->relativeUri, $this->readerResolver, $parent);
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
     * @return ResourceReaderResolver
     */
    protected function getReaderResolver()
    {
        return $this->readerResolver;
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
