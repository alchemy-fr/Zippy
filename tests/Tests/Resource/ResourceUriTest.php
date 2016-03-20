<?php

namespace Alchemy\Zippy\Tests\Resource;

use Alchemy\Zippy\Resource\ResourceUri;

class ResourceUriTest extends \PHPUnit_Framework_TestCase
{

    public function getUrisAndExpectedValidationResult()
    {
        return [
            [ 'file://', false ],
            [ '://', false ],
            [ ':///', false ],
            [ '/', false ],
            [ '/protocol/less/path', false ],
            [ 'protocol/less/relative/path', false ],
            [ 'file:///', true ],
            [ 'file:///absolute/path/', true ],
            [ 'file://relative/path/', true ],
            [ 'any://test', true ],
            [ 'daisy://file://path', true ],
            [ 'nested://daisy://file://path', true ],
            [ 'invalid://daisy://://path', false ],
            [ 'invalid://://daisy://path', false ]
        ];
    }

    /**
     * @dataProvider getUrisAndExpectedValidationResult
     * @param string $uri A valid URI
     * @param bool $expected The expected validation result
     */
    public function testUrisAreCorrectlyValidated($uri, $expected)
    {
        $this->assertEquals(
            $expected,
            \Alchemy\Zippy\Resource\ResourceUri::isValidUri($uri),
            'ResourceUri::isValidUri(' . $uri . ') should return ' . $expected ? 'true' : 'false'
        );
    }

    public function getChainedUris()
    {
        return [
            [ 'daisy://file://path', 'file://path' ],
            [ 'nested://daisy://file://path', 'daisy://file://path' ]
        ];
    }

    /**
     * @dataProvider getChainedUris
     * @param string $uri A chained resource URI
     * @param string $chainedResourceUri The chained resource's URI
     */
    public function testChainedResourcesAreCorrectlyDetected($uri, $chainedResourceUri)
    {
        $uri = new \Alchemy\Zippy\Resource\ResourceUri($uri);

        $this->assertTrue(
            $uri->hasChainedResource(),
            'ResourceUri[' . $uri . ']::hasChainedResource() should return true '
        );

        $this->assertEquals(
            $chainedResourceUri,
            $uri->getChainedResource()->getUri(),
            'ResourceUri[' . $uri . ']::getChainedResource() should return ResourceUri[' . $chainedResourceUri . '].'
        );
    }

    public function getMalformedUris()
    {
        return [
            [ 'file://' ],
            [ '://' ],
            [ ':///' ],
            [ 'invalid://daisy://://path' ],
            [ 'invalid://://daisy://path' ]
        ];
    }

    /**
     * @dataProvider getMalformedUris
     * @param string $uri A malformed URI chain
     */
    public function testMalformedUrisTriggerAnError($uri)
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        \Alchemy\Zippy\Resource\ResourceUri::fromString($uri);
    }

    public function getUrisWithoutProtocol()
    {
        return [
            [ '/', 'file:///' ],
            [ '/absolute/path', 'file:///absolute/path' ],
            [ 'relative/path', 'file://relative/path' ]
        ];
    }

    /**
     * @dataProvider getUrisWithoutProtocol
     * @param string $uri
     * @param string $expectedUri
     */
    public function testFromStringAppendsDefaultProtocol($uri, $expectedUri)
    {
        $resourceUri = ResourceUri::fromString($uri);

        $this->assertEquals(
            $expectedUri,
            $resourceUri->getUri(),
            'Parsing "' . $uri . '" should evaluate as "' . $expectedUri . '"'
        );
    }

    public function getUrisWithKnownProtocolAndResource()
    {
        return [
            [ 'file://resource', 'file', 'resource' ],
            [ 'http://resource', 'http', 'resource' ]
        ];
    }

    /**
     * @dataProvider getUrisWithKnownProtocolAndResource
     * @param string $uri
     * @param string $expectedProtocol
     * @param string $expectedResource
     */
    public function testProtocolAndResourceAreCorrectlyParsed($uri, $expectedProtocol, $expectedResource)
    {
        $resourceUri = new ResourceUri($uri);

        $this->assertEquals($expectedProtocol, $resourceUri->getProtocol());
        $this->assertEquals($expectedResource, $resourceUri->getResource());
    }

    public function testToStringReturnsSourceUri()
    {
        $resourceUri = new ResourceUri('file://path');

        $this->assertEquals('file://path', (string) $resourceUri);
    }
}
