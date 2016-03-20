<?php

namespace Alchemy\Zippy\Resource;

final class ResourceUri
{

    const DEFAULT_PROTOCOL = 'file';

    const PROTOCOL_SEPARATOR = '://';

    /**
     * @param $protocol
     * @param $resource
     * @return self
     */
    public static function fromProtocolAndResource($protocol, $resource)
    {
        return new self($protocol . self::PROTOCOL_SEPARATOR . $resource);
    }

    /**
     * @param $uri
     * @return bool
     */
    public static function isValidUri($uri)
    {
        if (strpos($uri, self::PROTOCOL_SEPARATOR) === false) {
            return false;
        }

        $parts = explode(self::PROTOCOL_SEPARATOR, $uri, 2);

        if (count(array_filter($parts, function ($value) { return $value != ''; })) !== 2) {
            return false;
        }

        if (strpos($parts[1], self::PROTOCOL_SEPARATOR) !== false) {
            return self::isValidUri($parts[1]);
        }

        return true;
    }

    public static function fromString($uri)
    {
        if (strpos($uri, self::PROTOCOL_SEPARATOR) === false) {
            $uri = self::DEFAULT_PROTOCOL . self::PROTOCOL_SEPARATOR . $uri;
        }

        return new self($uri);
    }

    public static function fromStringArray(array $uris)
    {
        $resourceUris = [];

        foreach ($uris as $uri) {
            $resourceUris[] = self::fromString($uri);
        }


        return $resourceUris;
    }

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    private $resource;

    /**
     * @param string $uri
     */
    public function __construct($uri)
    {
        if (! self::isValidUri($uri)) {
            throw new \InvalidArgumentException(sprintf(
                'Malformed URI: required format is "protocol://resource", got "%s"',
                $uri
            ));
        }

        $this->uri = $uri;

        list ($this->protocol, $this->resource) = explode('://', $uri, 2);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return bool
     */
    public function hasChainedResource()
    {
        return self::isValidUri($this->resource);
    }

    /**
     * @return self
     */
    public function getChainedResource()
    {
       return new self($this->resource);
    }

    /**
     * @param ResourceUri $other
     * @return bool
     */
    public function equals(ResourceUri $other)
    {
        return $this->getUri() == $other->getUri();
    }

    /**
     * @return ResourceUri
     */
    public function __clone()
    {
        return new self($this->uri);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uri;
    }
}
