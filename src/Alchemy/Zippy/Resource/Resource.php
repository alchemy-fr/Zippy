<?php

/*
 * This file is part of Zippy.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Zippy\Resource;

/**
 * This class represents a resource on a local filesystem
 */
class Resource
{
    /**
     * A path to a folder as the context
     *
     * @var String
     */
    private $context;

    /**
     * A resource relative path according to the context
     * @var String
     */
    private $relativepath;

    public function __constuct($workingDirectory, $relativepath)
    {
        $this->context = $workingDirectory;
        $this->relativepath = $relativepath;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($workingDirectory)
    {
        $this->context = $workingDirectory;
    }

    public function getRelativepath()
    {
        return $this->relativepath;
    }

    public function setRelativepath($relativepath)
    {
        $this->relativepath = $relativepath;
    }

    /**
     * Gets the full resource path
     *
     * @return String
     */
    public function getPath()
    {
         return sprintf('%s/%s', rtrim($this->context, '/'), $this->relativepath);
    }
}
