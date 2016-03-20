<?php

namespace Alchemy\Zippy\Adapter\Pecl\Rar;

use Alchemy\Zippy\Resource\Reader\RawStreamReader;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class RarResourceReaderResolver implements ResourceReaderResolver
{
    /**
     * \RarArchive
     */
    private $archive;

    public function __construct(\RarArchive $archive)
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
        return new RawStreamReader($this->archive->getEntry($resource->getResource())->getStream());
    }
}
