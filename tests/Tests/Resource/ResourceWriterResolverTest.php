<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Resource\ResourceUri;
use Alchemy\Zippy\Resource\ResourceWriter;
use Alchemy\Zippy\Resource\WriterResolver\ProtocolBasedWriterResolver;

class ResourceWriterResolverTest extends \PHPUnit_Framework_TestCase
{

    public function testResolverTriggersAnErrorForUnsupportedProtocols()
    {
        $this->setExpectedException(\RuntimeException::class);

        $resolver = new ProtocolBasedWriterResolver();
        $resource = new ResourceUri('file://tests/archives/test.zip');

        $resolver->resolveWriter($resource);
    }

    public function testResolverResolvesWriterForRegisteredProtocols()
    {
        $resolver = new ProtocolBasedWriterResolver();

        $fileWriter = $this->prophesize(ResourceWriter::class)->reveal();
        $zipWriter = $this->prophesize(ResourceWriter::class)->reveal();

        $resolver->addWriter($fileWriter, 'file');
        $resolver->addWriter($zipWriter, 'zip');

        $fileResource = ResourceUri::fromString('file://tests/archives/test/zip');
        $zipResource = ResourceUri::fromString('zip://file://tests/archives/test/zip');

        $this->assertSame($fileWriter, $resolver->resolveWriter($fileResource));
        $this->assertSame($zipWriter, $resolver->resolveWriter($zipResource));
    }
}
