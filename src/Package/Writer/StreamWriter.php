<?php

namespace Alchemy\Zippy\Package\Writer;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Resource\ResourceUri;

class StreamWriter
{

    public function write(PackagedResource $source, ResourceUri $destination)
    {
        $target = $destination->getResource() . '/' . $source->getRelativeUri()->getResource();

        file_put_contents($target, $source->getReader()->getContents());
    }
}
