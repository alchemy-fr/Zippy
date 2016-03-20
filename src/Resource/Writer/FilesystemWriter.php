<?php

namespace Alchemy\Zippy\Resource\Writer;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\ResourceWriter;

class FilesystemWriter implements ResourceWriter
{

    /**
     * @param ResourceReader $reader
     * @param ResourceUri $target
     */
    public function writeFromReader(ResourceReader $reader, ResourceUri $target)
    {
        file_put_contents($target->getResource(), $reader->getContentsAsStream());
    }
}
