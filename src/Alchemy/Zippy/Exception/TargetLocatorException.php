<?php

namespace Alchemy\Zippy\Exception;

class TargetLocatorException extends RuntimeException
{
    private $resource;

    public function __construct($resource, $message, $code = 0, $previous = null)
    {
        $this->resource = $resource;
        parent::__construct($message, $code, $previous);
    }

    public function getResource()
    {
        return $this->resource;
    }
}
