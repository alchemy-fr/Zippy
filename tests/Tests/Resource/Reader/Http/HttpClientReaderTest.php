<?php

namespace Alchemy\Zippy\Tests\Resource\Reader\Http;

use Alchemy\Zippy\Resource\Reader\Http\HttpClientReader;
use Alchemy\Zippy\Resource\Resource as ZippyResource;
use Alchemy\Zippy\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpClientReaderTest extends TestCase
{
    /**
     * @covers \Alchemy\Zippy\Resource\Reader\Http\HttpClientReader::getContents
     */
    public function testGetContents()
    {
        $reader = new HttpClientReader(
            new ZippyResource('http://example.org/badge.png', 'badge.png'),
            new MockHttpClient(new MockResponse('binary-content'))
        );

        $this->assertSame('binary-content', $reader->getContents());
    }

    /**
     * @covers \Alchemy\Zippy\Resource\Reader\Http\HttpClientReader::getContentsAsStream
     */
    public function testGetContentsAsStream()
    {
        $reader = new HttpClientReader(
            new ZippyResource('http://example.org/badge.png', 'badge.png'),
            new MockHttpClient(new MockResponse('binary-content'))
        );

        $stream = $reader->getContentsAsStream();

        $this->assertIsResource($stream);
        $this->assertSame('binary-content', stream_get_contents($stream));

        fclose($stream);
    }
}
