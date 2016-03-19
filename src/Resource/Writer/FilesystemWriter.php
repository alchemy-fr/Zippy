<?php

namespace Alchemy\Zippy\Resource\Writer;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceWriter;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemWriter implements ResourceWriter
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem = null)
    {
        $this->filesystem = $filesystem ?: new Filesystem();
    }

    /**
     * @param ResourceReader $reader
     * @param string $target
     */
    public function writeFromReader(ResourceReader $reader, $target)
    {
        $this->filesystem->dumpFile($target, $reader->getContents());
    }
}
