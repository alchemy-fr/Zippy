<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy;

use Alchemy\Zippy\Package\Package;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIteratorResolver;
use Alchemy\Zippy\Package\PackageWriter;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;
use Alchemy\Zippy\Resource\ResourceWriterResolver;

class Zippy
{
    /**
     * @var PackageWriter
     */
    private $packageWriter;

    /**
     * @var ResourceReaderResolver
     */
    private $resourceReaderResolver;

    /**
     * @var ResourceWriterResolver
     */
    private $resourceWriterResolver;

    /**
     * @var PackagedResourceIteratorResolver
     */
    private $packagedResourceIteratorResolver;

    public function __construct(
        ResourceReaderResolver $readerResolver = null,
        ResourceWriterResolver $writerResolver = null
    ) {
        $this->packagedResourceIteratorResolver = new PackagedResourceIteratorResolver();

        $this->resourceReaderResolver = $readerResolver ?: new ProtocolBasedReaderResolver();
        $this->resourceWriterResolver = $writerResolver ?: new ProtocolBasedWriterResolver();
    }

    /**
     * Creates an archive
     *
     * @param string $path
     * @param string|string[]|\Traversable|null $files
     * @param bool $recursive
     * @param string|null $type
     *
     */
    public function create($path, $files = null, $recursive = true, $type = null)
    {
        throw new \BadMethodCallException();
    }

    /**
     * Opens an archive.
     *
     * @param string $path
     * @return PackagedResource[]|\Traversable
     */
    public function open($path)
    {
        $resource = ResourceUri::fromString($path);

        return new Package(
            $resource,
            $this->resourceReaderResolver,
            $this->packagedResourceIteratorResolver
        );
    }

    /**
     * Creates Zippy and loads default strategies
     *
     * @return Zippy
     */
    public static function load()
    {
        return new self();
    }
}
