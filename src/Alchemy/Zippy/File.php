<?php

namespace Alchemy\Zippy;

class File
{
    /**
     * The location of the file
     *
     * @var     String
     */
    private $location;

    /**
     * Constructor
     */
    public function __construct($location)
    {
        $this->location = $location;
    }

    /**
     * @inheritdoc
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @inheritdoc
     */
    public function isDir()
    {

    }
}
