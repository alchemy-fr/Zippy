<?php

namespace Alchemy\Zippy\Exception;

class InvalidResourceException extends RuntimeException
{
    private $resource;

    public function __construct($resource, $message, $code, $previous)
    {
        $this->resource = $resource;
        parent::__construct($message, $code, $previous);
    }

    public function getResource()
    {
        return $this->resource;
    }
}
