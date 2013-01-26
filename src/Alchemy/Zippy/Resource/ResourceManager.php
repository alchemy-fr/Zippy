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

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException as SfIOException;
use Alchemy\Zippy\Exception\IOException;
use Alchemy\Zippy\Resource\ResourceMapper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class is responsible of handling resources retrievals mechanism
 */
class ResourceManager
{
    private $mapper;
    private $context;
    private $filesystem;

    public function __construct(ResourceMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->filesystem = new Filesystem();
    }

    /**
     * Loop throught the resource mapper and fetch resource to the approriate
     * context
     *
     * @return Array an array of Resource object
     *
     * @throws IOException In case temporary directory could not be created
     */
    public function handle()
    {
        $resources = $this->mapper->map();

        // default context is mapper context
        $this->context = $this->mapper->getContext();

        if ($this->requireTemporaryDirectory()) {
            // change context to temporary folder
            $this->context = sprintf('%s/%s', sys_get_temp_dir(), uniqid('zippy_'));

            try {
                $this->filesystem->mkdir($this->context);
            } catch (SfIOException $e) {
                throw new IOException(sprintf('Could not create temporary folder %s', $this->context), $e->getCode(), $e);
            }
        }

        $resourceCollection = array();

        // teleport all resource to the appropriate context
        foreach ($resources as $resourceTeleporter) {
            $resourceCollection[] = $resourceTeleporter->teleport($this->context);
        }

        return $resourceCollection;
    }

    /**
     * Remove temporary directory
     */
    public function deleteTemporaryFiles()
    {
        if ($this->requireTemporaryDirectory() && $this->context) {
            try {
                $this->filesystem->remove($this->context);
            } catch (IOException $e) {

            }
        }
    }

    /**
     * Tells whether if the fetched resources need the creation of a temporary
     * folder
     *
     * @return Boolean
     */
    public function requireTemporaryDirectory()
    {
        return count($this->mapper->getTemporaryFiles()) > 0;
    }

    public function getMapper()
    {
        return $this->mapper;
    }

    public function setMapper($mapper)
    {
        $this->mapper = $mapper;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }
}
