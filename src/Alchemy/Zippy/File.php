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
     * Gets location of the file
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets file location
     *
     * @param string $location the new location
     *
     * @return File
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
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
