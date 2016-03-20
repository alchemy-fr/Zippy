<?php

namespace Alchemy\Zippy\Adapter\Pecl\Zip;

use Alchemy\Zippy\Resource\Reader\Stream\RawStreamReader;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class ZipResourceReaderResolver implements ResourceReaderResolver
{
    /**
     * @var \ZipArchive
     */
    private $archive;

    public function __construct(\ZipArchive $archive)
    {
        $this->archive = $archive;
    }

    /**
     * Resolves a reader for the given resource URI.
     *
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function resolveReader(ResourceUri $resource)
    {
        return new RawStreamReader($this->archive->getStream($resource->getResource()));
    }
}
