<?php

namespace Alchemy\Zippy\Adapter\Pear\Tar;

use Alchemy\Zippy\Resource\Reader\StringReader;
use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class TarResourceReaderResolver implements ResourceReaderResolver
{
    /**
     * @var \Archive_Tar
     */
    private $archive;

    public function __construct(\Archive_Tar $archive)
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
        return new StringReader($this->archive->extractInString($resource->getResource()));
    }
}
