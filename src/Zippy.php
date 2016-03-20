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

use Alchemy\Zippy\Adapter\Pear\Tar\TarResourceIterator;
use Alchemy\Zippy\Adapter\Pecl\Rar\RarResourceIterator;
use Alchemy\Zippy\Adapter\Pecl\Zip\ZipResourceIterator;
use Alchemy\Zippy\Package\Package;
use Alchemy\Zippy\Package\PackageBuilder;
use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIteratorResolver;
use Alchemy\Zippy\Package\IteratorResolver\ProtocolBasedIteratorResolver;
use Alchemy\Zippy\Resource\PathUtil;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\ResourceReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\Writer\StreamWriter;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;
use Alchemy\Zippy\Resource\ResourceWriterResolver;

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

    public function __construct(
        ResourceReaderResolver $readerResolver = null,
        ResourceWriterResolver $writerResolver = null,
        PackagedResourceIteratorResolver $iteratorResolver = null
    ) {
        $this->packagedResourceIteratorResolver = $iteratorResolver ?: new ProtocolBasedIteratorResolver();

        $this->resourceReaderResolver = $readerResolver ?: new ProtocolBasedReaderResolver();
        $this->resourceWriterResolver = $writerResolver ?: new ProtocolBasedWriterResolver();
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

        if (! is_array($files) && ! $files instanceof \Traversable) {
            $files = [ $files ];
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
        $iteratorResolver = new ProtocolBasedIteratorResolver();
        $readerResolver = new ProtocolBasedReaderResolver();
        $writerResolver = new ProtocolBasedWriterResolver();

        $writerResolver->addWriter(new StreamWriter(), 'file');

        $iteratorResolver->addFactory('zip', function ($container) {
            return new ZipResourceIterator($container);
        });

        $iteratorResolver->addFactory('rar', function ($container) {
            return new RarResourceIterator($container);
        });

        $iteratorResolver->addFactory('tar', function ($container) {
            return new TarResourceIterator($container);
        });

        $iteratorResolver->addFactory('file', function (PackagedResource $container) use ($iteratorResolver) {
            $extension = PathUtil::extractExtension($container->getRelativeUri()->getResource());
            $factory = $iteratorResolver->getFactory($extension);

            return $factory($container);
        });

        return new self($readerResolver, $writerResolver, $iteratorResolver);
    }
}
