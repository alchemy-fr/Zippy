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
 * This class is responsible of looping throught a set of provided
 * resource, separate the resources that belong to the reference context from them
 * that do not and getting the appropriate ResourceTeleporter object for each
 * resource
 */
class RequestMapper
{
    private $locator;

    public function __construct(TargetLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * Maps resources request to a ResourceCollection
     *
     * @return ResourceRequest
     */
    public function map($context, array $resources)
    {
        $collection = new ResourceCollection($context);

        foreach ($resources as $location => $resource) {
            if (is_int($location)) {
                $collection->add(new Resource($resource, $this->locator->locate($context, $resource)));
            } else {
                $collection->add(new Resource($resource, ltrim($location, '/')));
            }
        }

        return $collection;
    }

    public static function create()
    {
        return new static(new TargetLocator());
    }
}
