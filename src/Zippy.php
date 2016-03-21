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
use Alchemy\Zippy\Package\PackageBuilder;
use Alchemy\Zippy\Package\PackagedResourceIteratorResolver;
use Alchemy\Resource\ResourceReaderResolver;
use Alchemy\Resource\ResourceUri;
use Alchemy\Resource\ResourceWriterResolver;

class Zippy
{

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

    /**
     * @param ResourceReaderResolver $readerResolver
     * @param ResourceWriterResolver $writerResolver
     * @param PackagedResourceIteratorResolver $iteratorResolver
     */
    public function __construct(
        ResourceReaderResolver $readerResolver,
        ResourceWriterResolver $writerResolver,
        PackagedResourceIteratorResolver $iteratorResolver
    ) {
        $this->packagedResourceIteratorResolver = $iteratorResolver;
        $this->resourceReaderResolver = $readerResolver;
        $this->resourceWriterResolver = $writerResolver;
    }

    /**
     * Creates an archive
     *
     * @param string $path
     * @param string|string[]|\Traversable|null $files
     * @return PackageBuilder
     */
    public function create($path, $files = null)
    {
        $resource = ResourceUri::fromString($path);
        $package = new PackageBuilder(
            $resource,
            $this->resourceReaderResolver,
            $this->resourceWriterResolver,
            $this->packagedResourceIteratorResolver
        );

        if (!is_array($files) && !$files instanceof \Traversable) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $package->addResource(ResourceUri::fromString($file));
        }

        return $package;
    }

    /**
     * Opens an archive.
     *
     * @param string $path
     * @return Package
     */
    public function open($path)
    {
        $resource = ResourceUri::fromString($path);

        return new Package(
            $resource,
            $this->resourceReaderResolver,
            $this->resourceWriterResolver,
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
        return (new ZippyFactory())->create();
    }
}
