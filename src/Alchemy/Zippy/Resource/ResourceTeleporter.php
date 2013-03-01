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
 * This object is responsible of teleporting a resource from an URI to a
 * local filesystem destination
 */
class ResourceTeleporter
{
    private $container;

    public function __construct(TeleporterContainer $container)
    {
        $this->container = $container;
    }

    public function teleport($context, Resource $resource)
    {
        $this
            ->container
            ->fromResource($resource)
            ->teleport($resource, $context);

        return $this;
    }

    public static function create()
    {
        return new static(TeleporterContainer::load());
    }
}
