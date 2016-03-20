<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Resource\ResourceReader;
use Alchemy\Zippy\Resource\ResourceReaderFactory;
use Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver;
use Alchemy\Zippy\Resource\ResourceUri;

class ResourceReaderResolverTest extends \PHPUnit_Framework_TestCase
{

    public function testResolverTriggersAnErrorForUnsupportedProtocols()
    {
        $this->setExpectedException(\RuntimeException::class);

        $resolver = new ProtocolBasedReaderResolver();
        $resource = new ResourceUri('file://tests/archives/test.zip');

        $resolver->resolveReader($resource);
    }

    public function testResolverResolvesWriterForRegisteredProtocols()
    {
        $resolver = new \Alchemy\Zippy\Resource\ReaderResolver\ProtocolBasedReaderResolver();

        $fileReader = $this->prophesize(ResourceReader::class)->reveal();
        $zipReader = $this->prophesize(ResourceReader::class)->reveal();

        $fileFactory = new MockReaderFactory($fileReader);
        $zipFactory = new MockReaderFactory($zipReader);

        $resolver->addFactory($fileFactory, 'file');
        $resolver->addFactory($zipFactory, 'zip');

        $fileResource = ResourceUri::fromString('file://tests/archives/test/zip');
        $zipResource = ResourceUri::fromString('zip://file://tests/archives/test/zip');

        $this->assertSame($fileReader, $resolver->resolveReader($fileResource));
        $this->assertSame($zipReader, $resolver->resolveReader($zipResource));
    }
}

class MockReaderFactory implements ResourceReaderFactory
{
    private $reader;

    public function __construct(ResourceReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param ResourceUri $resource
     * @return ResourceReader
     */
    public function getReader(ResourceUri $resource)
    {
        return $this->reader;
    }
}
