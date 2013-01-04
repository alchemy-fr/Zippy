<?php

namespace Alchemy\Zippy;

class File
{
    /**
     * The location of the file
     *
     * @var     string
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
     * Tells wethe the file is a directory or not
     *
     * @return Boolean
     */
    public function isDir()
    {

    }
}
