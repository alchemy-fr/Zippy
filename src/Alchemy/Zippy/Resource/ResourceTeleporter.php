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

use Alchemy\Zippy\Resource\Teleporter\TeleporterInterface;

/**
 * This object is responsible of teleporting a resource from an URI to a
 * local filesystem destination
 */
class ResourceTeleporter
{
    /**
     * A TeleporterInterface instance
     *
     * @var TeleporterInterface
     */
    private $teleporter;

    /**
     * A resource URI
     *
     * @var String
     */
    private $uri;

    /**
     * A resource destination
     * @var String
     */
    private $target;

    public function __construct(TeleporterInterface $teleporter, $uri, $target)
    {
        $this->teleporter = $teleporter;
        $this->uri = $uri;
        $this->target = $target;
    }

    /**
     * Teleport a resource according to the given context
     *
     * @param String $context A path to a folder as the context
     *
     * @return Resource
     */
    public function teleport($context)
    {
        $destination = sprintf('%s/%s', rtrim($context, '/'), $this->target);

        $this->teleporter->teleport($this->uri, $destination);

        return new Resource($context, $this->target);
    }

    public function getTeleporter()
    {
        return $this->teleporter;
    }

    public function setTeleporter($teleporter)
    {
        $this->teleporter = $teleporter;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }
}
